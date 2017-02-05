<?php

namespace Notifier;

// error_reporting ( E_ALL );
use Notifier\Config\Config;
use Notifier\Config\LockFile;
use Notifier\Data\ConnectionData;
use Notifier\Data\MessageSeverity;
use Notifier\Incoming\Gmail;
use Notifier\Outgoing\EmailToSmsMtel;
use Notifier\PolicyEngine\Then\Action\BeepSendSmsAction;
use Notifier\PolicyEngine\Then\Action\EmailToSmsMtelAction;
use Notifier\PolicyEngine\Then\Modification\ParadoxMessageDataSeverityMod;
use Notifier\PolicyEngine\Then\Modification\ParadoxMessageDataSimplifyMod;
use Notifier\Utils\Utils;

require_once __DIR__ . '/Data/ConnectionData.php';
require_once __DIR__ . '/Data/MessageSeverity.php';
require_once __DIR__ . '/Data/MessageData.php';
require_once __DIR__ . '/Incoming/Gmail.php';
require_once __DIR__ . '/Config/Config.php';
require_once __DIR__ . '/Config/LockFile.php';
require_once __DIR__ . '/PolicyEngine/Then/Action/EmailToSmsMtelAction.php';
require_once __DIR__ . '/PolicyEngine/Then/Action/BeepSendSmsAction.php';
require_once __DIR__ . '/PolicyEngine/Then/Modification/ParadoxMessageDataSimplifyMod.php';
require_once __DIR__ . '/PolicyEngine/Then/Modification/ParadoxMessageDataSeverityMod.php';

if (Utils::isProgramRunWindow ()) {
	try {
		LockFile::acquire_or_die ();
		
		$gmailConnectionData = Config::getConfigData ( "ConnectionDataGmail" );
		$mtelConnectionData = Config::getConfigData ( "ConnectionDataMtelSMTP" );
		$beepSendSmsConnectionData = Config::getConfigData ( "ConnectionDataBeepSend" );
		
		while ( Utils::isProgramRunWindow () ) {
			// processing loop
			$gmail = new Gmail ( $gmailConnectionData );
			$newMessageDataCount = $gmail->getNewMessageDataCount ();
			if ($newMessageDataCount > 0) {
				// Get the message data
				$messageDataList = $gmail->getNewMessageData ();
				
				// ::::Modifications
				// Simplify the messages if they are Paradox Message data
				$simplifyMod = new ParadoxMessageDataSimplifyMod ();
				$simplifyMod->perform ( $messageDataList );
				
				// Set the right severity if they are Paradox Message data
				$setSerevityMod = new ParadoxMessageDataSeverityMod ();
				$setSerevityMod->perform ( $messageDataList );
				
				// ::::Actions
				// Send SMS if needed
				$beepSendSms = new BeepSendSmsAction ( $beepSendSmsConnectionData );
				$beepSendSms->notify ( $messageDataList );
				
				// Send Email to SMS message
				$emailToSmsMtel = new EmailToSmsMtelAction ( $mtelConnectionData );
				$emailToSmsMtel->notify ( $messageDataList );
			}
		}
	} finally {
		LockFile::release ();
	}
}
