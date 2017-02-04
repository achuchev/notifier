<?php

namespace Notifier\PolicyEngine\Then\Action;

use Notifier\Config\Config;
use Notifier\Data\MessageSeverity;
use Notifier\Logging\Logger;
use Notifier\Utils\EmailUtils;
use Notifier\Utils\Utils;

require_once __DIR__ . '/ActionBase.php';
require_once __DIR__ . '/../../../Logging/Logger.php';
require_once __DIR__ . '/../../../Utils/EmailUtils.php';
require_once __DIR__ . '/../../../Utils/Utils.php';
require_once __DIR__ . '/../../../Config/Config.php';
require_once __DIR__ . '/../../../Data/MessageSeverity.php';
class EmailToSmsMtelAction extends ActionBase {
	const MTEL_DOMAIN = "@sms.mtel.net";
	public function notify($messageDataList, $notifySeverity = MessageSeverity::Low) {
		foreach ( $messageDataList as $messageData ) {
			Logger::info ( "Sending Email (SMS) message." );
			if ($messageData->severity >= $notifySeverity) {
				Logger::debug ( "Skipping SMS using BeepSend as the severity is lower." );
				continue;
			}
			
			$sender = EmailUtils::simplifyEmailAddress ( $messageData->sender );
			$recipient = Utils::preparePhoneNumber ( $this->connectionData->username ) . self::MTEL_DOMAIN;
			$headers = 'From: ' . $sender . "\r\n" . 'Reply-To: ' . $sender . "\r\n" . 'X-Mailer: PHP/' . phpversion ();
			
			$maxBodyLength = EmailUtils::getMaxBodyLength ( $messageData->subject, $sender );
			$bodyParts = EmailUtils::simplifyEmailBody ( $messageData->body, $maxBodyLength );
			
			if (Config::isTestMode () == TRUE) {
				Logger::info ( "FAKE Email (SMS) message sent to " . $recipient );
				break;
			}
			
			Logger::info ( "Sending Email (SMS) message in " . count ( $bodyParts ) . " parts to " . $recipient );
			foreach ( $bodyParts as $body ) {
				Logger::debug ( "\r\n\tSender: " . $messageData->sender . "\r\n\tRecipient: " . $recipient . "\r\n\tOriginal Recipient: " . $messageData->recipient . "\r\n\tSubject: " . $messageData->subject . "\r\n\tBody: " . $body );
				
				$status = mail ( $recipient, $messageData->subject, $body, $headers );
				if ($status === TRUE) {
					Logger::info ( "Email (SMS) message sent to " . $recipient );
				} else {
					throw new \Exception ( "Could not send Email (SMS) message to " . $recipient );
				}
				// Sleep between the send opeations. Hope that the messages will be oreder.
				sleep ( 3 );
			}
		}
	}
}