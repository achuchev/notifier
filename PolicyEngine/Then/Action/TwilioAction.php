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
		$makeACall = FALSE;
		foreach ( $messageDataList as $messageData ) {
			if ($messageData->severity >= $notifySeverity) {
				$makeACall = TRUE;
				break;
			}
		}
		if ($makeACall == TRUE) {
			if (Config::isTestMode () == TRUE) {
				Logger::info ( "Making Twilio Voice call to " . $this->connectionData->folder );
				return;
			}
			Logger::info ( "Making Twilio Voice call to ." . $this->connectionData->folder );
			$client = new Client ( $this->connectionData->username, $this->connectionData->password );
			try {
				// Initiate a new outbound call
				$call = $client->account->calls->create ( $this->connectionData->folder, $this->connectionData->hostname, array (
						"url" => "http://demo.twilio.com/welcome/voice/" 
				) );
				Logger::debug ( "Started call: " . $call->sid );
			} catch ( Exception $error ) {
				Logger::error ( "Error: " . $error->getMessage () );
			}
		} else {
			Logger::debug ( "None of the messages require voice call." );
		}
	}
}
