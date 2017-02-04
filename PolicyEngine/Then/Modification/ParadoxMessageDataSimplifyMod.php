<?php

namespace Notifier\PolicyEngine\Then\Modification;

use Notifier\Logging\Logger;
use Notifier\PolicyEngine\Then\Modification\ModificationBase;

require_once __DIR__ . '/ModificationBase.php';
require_once __DIR__ . '/../../../Logging/Logger.php';
class ParadoxMessageDataSimplifyMod extends ModificationBase {
	private static $linesToDelete = array (
			"From your Paradox internet module" 
	);
	private static $stringsToReplace = array (
			"Site: " => "S: ",
			"Message: " => "Msg: ",
			"Partition: " => "P: ",
			"Time: " => "T: " 
	);
	public function perform($messageDataList) {
		foreach ( $messageDataList as $messageData ) {
			Logger::info ( "Simplifying Paradox Message Data" );
			foreach ( self::$linesToDelete as $lineToDelete ) {
				$newBodyLines = [ ];
				foreach ( preg_split ( "/(\r?\n)/", $messageData->body ) as $line ) {
					if (substr ( $line, 0, strlen ( $lineToDelete ) ) != $lineToDelete) {
						array_push ( $newBodyLines, $line );
					}
				}
				$messageData->body = implode ( "\r\n", $newBodyLines );
				
				foreach ( self::$stringsToReplace as $searchStr => $replaceStr ) {
					$messageData->body = str_replace ( $searchStr, $replaceStr, $messageData->body );
				}
			}
		}
	}
}