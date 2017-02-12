<?php

namespace Notifier\PolicyEngine\Then\Action;

use Notifier\Config\Config;
use Notifier\Data\MessageSeverity;
use Notifier\Logging\Logger;
use Notifier\Utils\EmailUtils;
use Notifier\Utils\Utils;
use Twilio\Rest\Client;

require_once __DIR__ . '/ActionBase.php';
require_once __DIR__ . '/../../../Logging/Logger.php';
require_once __DIR__ . '/../../../Utils/EmailUtils.php';
require_once __DIR__ . '/../../../Utils/Utils.php';
require_once __DIR__ . '/../../../Config/Config.php';
require_once __DIR__ . '/../../../Data/MessageSeverity.php';
require_once __DIR__ . '/../../../Libs/Twilio/autoload.php';
class TwilioAction extends ActionBase {
	public function notify($messageDataList, $notifySeverity = MessageSeverity::High) {
		Logger::info ( "Making Twilio Voice call." );
		$accountsDataToCall = [ ];
		foreach ( $messageDataList as $messageData ) {
			if ($messageData->severity >= $notifySeverity) {
				array_push ( $accountsDataToCall, $messageData->accountData );
			}
		}
		if (count ( $accountsDataToCall ) <= 0) {
			Logger::debug ( "None of the messages require voice call." );
			return;
		}
		foreach ( $accountsDataToCall as $accountData ) {
			if (Config::isTestMode () == TRUE) {
				Logger::info ( "Making Twilio Voice call to " . $accountData->connectionDataVoice->folder );
				return;
			}
			Logger::info ( "Making Twilio Voice call to ." . $accountData->connectionDataVoice->folder );
			$client = new Client ( $accountData->connectionDataVoice->username, $accountData->connectionDataVoice->password );
			try {
				// Initiate a new outbound call
				$call = $client->account->calls->create ( $accountData->connectionDataVoice->folder, $accountData->connectionDataVoice->hostname, array (
						"url" => "http://demo.twilio.com/welcome/voice/" 
				) );
				Logger::debug ( "Started call: " . $call->sid );
			} catch ( Exception $error ) {
				Logger::error ( "Error: " . $error->getMessage () );
			}
		}
	}
}
