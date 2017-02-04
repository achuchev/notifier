<?php

namespace Notifier\Logging;

use Notifier\Config\Config;
use Notifier\Utils\Utils;

require_once __DIR__ . '/../Config/Config.php';
require_once __DIR__ . '/../Utils/Utils.php';
abstract class LoggingLevel {
	const Debug = 0;
	const Info = 1;
	const Warn = 2;
	const Error = 3;
	public static function getLabel($logginglevel) {
		switch ($logginglevel) {
			case LoggingLevel::Debug :
				return "debug";
			case LoggingLevel::Info :
				return "info";
			case LoggingLevel::Warn :
				return "warn";
			case LoggingLevel::Error :
				return "error";
		}
	}
	public static function getMember($logginglevelStr) {
		$logginglevelStr = strtolower ( $logginglevelStr );
		switch ($logginglevelStr) {
			case "debug" :
				return LoggingLevel::Debug;
			case "info" :
				return LoggingLevel::Info;
			case "warn" :
				return LoggingLevel::Warn;
			case "error" :
				return LoggingLevel::Error;
		}
	}
}
class Logger {
	private static $loggingLevel = LoggingLevel::Debug;
	const END_LINE_CHAR_HTML = "<br />";
	const END_LINE_CHAR_TXT = "\r\n";
	private static $endLineCharacter = self::END_LINE_CHAR_TXT;
	static private function log($level, $message) {
		if (self::$loggingLevel == NULL) {
			self::$loggingLevel = LoggingLevel::getMember ( Config::getProperty ( "Logging", "level" ) );
			
			if (Config::getProperty ( "Logging", "outputHTML" ) == TRUE) {
				self::$endLineCharacter = self::END_LINE_CHAR_HTML;
			} else {
				self::$endLineCharacter = self::END_LINE_CHAR_TXT;
			}
		}
		if ($level >= self::$loggingLevel) {
			$levelStr = strtoupper ( LoggingLevel::getLabel ( $level ) );
			echo Utils::timestampToDate ( time () ) . " " . $levelStr . ":\t" . $message . self::$endLineCharacter;
		}
	}
	public static function info($message) {
		Logger::log ( LoggingLevel::Info, $message );
	}
	public static function debug($message) {
		Logger::log ( LoggingLevel::Debug, $message );
	}
	public static function warning($message) {
		Logger::log ( LoggingLevel::Warn, $message );
	}
	public static function error($message) {
		Logger::log ( LoggingLevel::Error, $message );
	}
}