<?php

namespace Notifier\PolicyEngine\Then\Action;

use Beepsend;
use Beepsend\Client;
use Notifier\Config\Config;
use Notifier\Data\MessageSeverity;
use Notifier\Logging\Logger;
use Notifier\Utils\Utils;

require_once __DIR__ . '/ActionBase.php';
require_once __DIR__ . '/../../../Logging/Logger.php';
require_once __DIR__ . '/../../../Libs/Beepsend/Client.php';
require_once __DIR__ . '/../../../Utils/Utils.php';
require_once __DIR__ . '/../../../Config/Config.php';
require_once __DIR__ . '/../../../Data/MessageSeverity.php';
class BeepSendSmsAction extends ActionBase {
	public function notify($messageDataList, $notifySeverity = MessageSeverity::High) {
		$options = array (
				'receive_dlr' => 0 
		);
		
		foreach ( $messageDataList as $messageData ) {
			$phoneNumber = Utils::preparePhoneNumber ( $messageData->accountData->connectionDataSMS->username );
			Logger::info ( "Sending SMS using BeepSend." );
			if ($messageData->severity < $notifySeverity) {
				Logger::debug ( "Skipping SMS using BeepSend as the severity is lower." );
				continue;
			}
			
			try {
				$client = new Client ( $messageData->accountData->connectionDataSMS->password );
				$text = $messageData->subject . ' ' . $messageData->body;
				if (Config::isTestMode () == TRUE) {
					Logger::info ( "FAKE SMS (using BeepSend) to to " . $phoneNumber );
					break;
				}
				
				$response = $client->message->send ( $phoneNumber, 'Notifier', $text, null, 'UTF-8', $options );
				Logger::debug ( "Response: " . json_encode ( $response, JSON_PRETTY_PRINT ) );
			} catch ( \Exception $error ) {
				Logger::error ( "Could not send SMS (using BeepSend) to " . $phoneNumber . " Error: " . $error->getMessage () );
			}
		}
	}
}


