<?php

namespace Notifier\Utils;

use Notifier\Logging\Logger;
use Notifier\Config\Config;

require_once __DIR__ . '/../Config/Config.php';
require_once __DIR__ . '/../Logging/Logger.php';
class Utils {
	public static $programStartTime = NULL;
	private static $programEndTime = NULL;
	public static function IsNullOrEmptyString($string) {
		// Function for basic field validation (present and neither empty nor only white space
		return (! isset ( $string ) || trim ( $string ) === '');
	}
	public static function strStartsWith($string, $query) {
		// Function to check if string starts with substring
		return substr ( $string, 0, strlen ( $query ) ) === $query;
	}
	public static function strEndsWith($string, $query) {
		// Function to check if string ends with substring
		return (substr ( $string, - strlen ( $query ) ) === $query);
	}
	public static function preparePhoneNumber($phoneNumber) {
		$badLeadingChars = array (
				'0',
				'00',
				'+' 
		);
		foreach ( $badLeadingChars as $badLeandingChar ) {
			// remove the bad leading chars
			if (Utils::strStartsWith ( $phoneNumber, $badLeandingChar )) {
				$phoneNumber = str_replace ( $badLeandingChar, '', $phoneNumber );
			}
		}
		if (! (Utils::strStartsWith ( $phoneNumber, '359' ))) {
			$phoneNumber = "359" . $phoneNumber;
		}
		return $phoneNumber;
	}
	public static function timestampToDate($timestamp) {
		return date ( "Y-m-d H:i:s", $timestamp );
	}
	public static function isProgramRunWindow() {
		if (Utils::IsNullOrEmptyString ( self::$programStartTime )) {
			self::$programStartTime = time ();
			Logger::debug ( "Program started at " . self::timestampToDate ( self::$programStartTime ) );
			self::$programEndTime = self::$programStartTime + Config::getProperty ( 'Program', 'maxRunTime' );
			Logger::debug ( "Program will end at " . self::timestampToDate ( self::$programEndTime ) );
		}
		if (time () >= self::$programEndTime) {
			Logger::debug ( "Program end time reached. Exiting." );
			return FALSE;
		}
		sleep ( Config::getProperty ( 'Program', 'sleepBetweenIterations' ) );
		return TRUE;
	}
}