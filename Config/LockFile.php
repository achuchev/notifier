<?php

namespace Notifier\Config;

use Notifier\Logging\Logger;

require_once __DIR__ . '/../Logging/Logger.php';
class LockFile {
	const LOCK_FILE_NAME = 'notifier.in.progress';
	const SLEEP_TIME = 5;
	public static function acquire_or_die() {
		$lockFileWaitSeconds = Config::getProperty ( "Program", "lockFileWaitSeconds" );
		while ( TRUE ) {
			if (file_exists ( self::LOCK_FILE_NAME )) {
				if ($lockFileWaitSeconds > 0) {
					$lockFileWaitSeconds = $lockFileWaitSeconds - self::SLEEP_TIME;
					Logger::debug ( "Lock file still exists. Wait time left " . $lockFileWaitSeconds . " seconds." );
					sleep ( self::SLEEP_TIME );
					continue;
				} else {
					exit ( "Lock file '" . self::LOCK_FILE_NAME . "' exists. Exiting..." );
				}
			} else {
				break;
			}
		}
		$success = touch ( self::LOCK_FILE_NAME );
		if ($success) {
			Logger::debug ( "Lock file '" . self::LOCK_FILE_NAME . "' created." );
		} else {
			exit ( "Could not create lock file '" . self::LOCK_FILE_NAME . "'. Exiting..." );
		}
	}
	public static function release() {
		$success = unlink ( self::LOCK_FILE_NAME );
		if ($success) {
			Logger::debug ( "Lock file '" . self::LOCK_FILE_NAME . "' removed." );
		} else {
			Logger::error ( "Could not remove lock file '" . self::LOCK_FILE_NAME . "'. Exiting..." );
		}
	}
}
