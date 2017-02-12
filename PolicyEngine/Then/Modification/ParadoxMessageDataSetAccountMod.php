<?php

namespace Notifier\PolicyEngine\Then\Modification;

use Notifier\Logging\Logger;
use Notifier\PolicyEngine\Then\Modification\ModificationBase;
use Notifier\Utils\Utils;
use Notifier\Config\Config;

require_once __DIR__ . '/ModificationBase.php';
require_once __DIR__ . '/../../../Logging/Logger.php';
require_once __DIR__ . '/../../../Utils/Utils.php';
require_once __DIR__ . '/../../../Config/Config.php';
class ParadoxMessageSetAccountMod extends ModificationBase {
	public function perform($messageDataList) {
		$filteredMessageDataList = [ ];
		foreach ( $messageDataList as $messageData ) {
			Logger::info ( "Set Account Paradox Message Data" );
			$accountsData = Config::getAllAccountsData ();
			foreach ( $accountsData as $accountData ) {
				$site_text = "Site: " . $accountData->paradoxSite;
				Logger::debug ( "Detecting Site. Checking for '" . $site_text . "'" );
				if (Utils::strContains ( $messageData->body, $site_text )) {
					$messageData->accountData = $accountData;
					Logger::debug ( "Set account data to message data with subject '" . $messageData->subject . "'" );
					array_push ( $filteredMessageDataList, $messageData );
					break;
				}
			}
			Logger::warning ( "Site not detected for message with subject '" . $messageData->subject . "'. The message will be skipped." );
		}
		return $filteredMessageDataList;
	}
}