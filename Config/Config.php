<?php

namespace Notifier\Config;

use Notifier\Data\ConnectionData;

require_once __DIR__ . '/../Data/ConnectionData.php';
class Config {
	const CONFIG_FILE_NAME = "config.ini";
	const CONFIG_FILE_PATH = "../../../notifier_conf/config.ini";
	private static $configArray = NULL;
	private static function loadConfigIfNeeded() {
		if (self::$configArray === NULL) {
			$conf_file = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME;
			if (! (file_exists ( $conf_file ))) {
				$conf_file = (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::CONFIG_FILE_PATH);
				if (! (file_exists ( $conf_file ))) {
					// we cannot work without config file
					exit ( "Could not load configuration file " . self::CONFIG_FILE_NAME . " from current folder or " . $conf_file );
				}
			}
			// Parse with sections
			self::$configArray = parse_ini_file ( $conf_file, TRUE );
		}
	}
	public static function getSection($sectionName) {
		self::loadConfigIfNeeded ();
		if (array_key_exists ( $sectionName, self::$configArray )) {
			return self::$configArray [$sectionName];
		} else {
			throw new \Exception ( "Could not get section with name " . $sectionName );
		}
	}
	public static function getProperty($sectionName, $propertyName) {
		$section = self::getSection ( $sectionName );
		if (array_key_exists ( $propertyName, $section )) {
			return $section [$propertyName];
		} else {
			throw new \Exception ( "Could not get property with name " . $propertyName . " from section with name " . $sectionName );
		}
	}
	public static function getConfigData($sectionName) {
		$configDataSection = self::getSection ( $sectionName );
		return new ConnectionData ( $configDataSection ['hostname'], $configDataSection ['username'], $configDataSection ['password'], $configDataSection ['folder'] );
	}
	public static function isTestMode() {
		return self::getProperty ( "General", "testMode" );
	}
}

