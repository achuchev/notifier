<?php

namespace Notifier;

use Notifier\Config\Config;
use Notifier\Config\LockFile;
use Notifier\Data\ConnectionData;
use Notifier\Data\MessageSeverity;
use Notifier\Incoming\Gmail;
use Notifier\PolicyEngine\Then\Action\BeepSendSmsAction;
use Notifier\PolicyEngine\Then\Action\EmailToSmsMtelAction;
use Notifier\PolicyEngine\Then\Action\TwilioAction;
use Notifier\PolicyEngine\Then\Modification\ParadoxMessageDataSeverityMod;
use Notifier\PolicyEngine\Then\Modification\ParadoxMessageDataSimplifyMod;
use Notifier\Utils\Utils;
use Notifier\PolicyEngine\Then\Modification\ParadoxMessageSetAccountMod;

require_once __DIR__ . '/Data/ConnectionData.php';
require_once __DIR__ . '/Data/MessageSeverity.php';
require_once __DIR__ . '/Data/MessageData.php';
require_once __DIR__ . '/Incoming/Gmail.php';
require_once __DIR__ . '/Config/Config.php';
require_once __DIR__ . '/Config/LockFile.php';
require_once __DIR__ . '/PolicyEngine/Then/Action/EmailToSmsMtelAction.php';
require_once __DIR__ . '/PolicyEngine/Then/Action/BeepSendSmsAction.php';
require_once __DIR__ . '/PolicyEngine/Then/Action/TwilioAction.php';
require_once __DIR__ . '/PolicyEngine/Then/Modification/ParadoxMessageDataSimplifyMod.php';
require_once __DIR__ . '/PolicyEngine/Then/Modification/ParadoxMessageDataSeverityMod.php';
require_once __DIR__ . '/PolicyEngine/Then/Modification/ParadoxMessageDataSetAccountMod.php';

if (Utils::isProgramRunWindow ()) {
	try {
		LockFile::acquire_or_die ();
		
		$gmailConnectionData = Config::getConnectionData ( "ConnectionDataGmail" );
		
		while ( Utils::isProgramRunWindow () ) {
			// processing loop
			$gmail = new Gmail ( $gmailConnectionData );
			$newMessageDataCount = $gmail->getNewMessageDataCount ();
			if ($newMessageDataCount > 0) {
				// Get the message data
				$messageDataList = $gmail->getNewMessageData ();
				
				// ::::Modifications
				// As a first step, set the account data to the messages
				$setAccountMod = new ParadoxMessageSetAccountMod ();
				$messageDataList = $setAccountMod->perform ( $messageDataList );
				
				if (empty ( $messageDataList )) {
					// All messages are filtered out
					break;
				}
				
				// Simplify the messages if they are Paradox Message data
				$simplifyMod = new ParadoxMessageDataSimplifyMod ();
				$simplifyMod->perform ( $messageDataList );
				
				// Set the right severity if they are Paradox Message data
				$setSerevityMod = new ParadoxMessageDataSeverityMod ();
				$setSerevityMod->perform ( $messageDataList );
				
				// ::::Actions
				// Make a voice call
				$twilioVoice = new TwilioAction ();
				$twilioVoice->notify ( $messageDataList );
				
				// Send SMS if needed
				$beepSendSms = new BeepSendSmsAction ();
				$beepSendSms->notify ( $messageDataList );
				
				// Send Email to SMS message
				$emailToSmsMtel = new EmailToSmsMtelAction ();
				$emailToSmsMtel->notify ( $messageDataList );
			}
		}
	} finally {
		LockFile::release ();
	}
}
