<?php

namespace Notifier\PolicyEngine\Then\Modification;

use Notifier\Data\MessageSeverity;
use Notifier\Logging\Logger;
use Notifier\PolicyEngine\Then\Modification\ModificationBase;
use Notifier\Utils\Utils;

require_once __DIR__ . '/ModificationBase.php';
require_once __DIR__ . '/../../../Logging/Logger.php';
require_once __DIR__ . '/../../../Utils/Utils.php';
require_once __DIR__ . '/../../../Data/MessageSeverity.php';
class ParadoxMessageDataSeverityMod extends ModificationBase {
	public function perform($messageDataList) {
		foreach ( $messageDataList as $messageData ) {
			Logger::info ( "Set Severity to Paradox Message Data" );
			if (Utils::strStartsWith ( $messageData->subject, "Alarm" )) {
				$messageData->severity = MessageSeverity::VeryHigh;
				Logger::debug ( "Severity set to VeryHigh for message data with subject '" . $messageData->subject . "'" );
			}
		}
	}
}