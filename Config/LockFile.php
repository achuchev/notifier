<?php

namespace Notifier\Config;

use Notifier\Logging\Logger;

require_once __DIR__ . '/../Logging/Logger.php';
class LockFile {
	const LOCK_FILE_NAME = '/notifier.in.progress';
	const SLEEP_TIME = 5;
	public static function acquire_or_die() {
		$lockFileWaitSeconds = Config::getProperty ( "Program", "lockFileWaitSeconds" );
		while ( TRUE ) {
			if (file_exists ( self::getLockFilePath () )) {
				if ($lockFileWaitSeconds > 0) {
					$lockFileWaitSeconds = $lockFileWaitSeconds - self::SLEEP_TIME;
					Logger::debug ( "Lock file still exists. Wait time left " . $lockFileWaitSeconds . " seconds." );
					sleep ( self::SLEEP_TIME );
					
					$lock_file_ctime = filectime ( self::getLockFilePath () );
					// Max lock time is twice the maxRunTime
					$max_lock_file_ctime = $lock_file_ctime + (2 * Config::getMaxRunTime ());
					if (time () >= $max_lock_file_ctime) {
						Logger::warning ( "Lock file is too old (" . date ( "F d Y H:i:s", $lock_file_ctime ) . "). Deleting it." );
						self::release ();
					}
					continue;
				} else {
					exit ( "Lock file '" . self::getLockFilePath () . "' exists. Exiting..." );
				}
			} else {
				break;
			}
		}
		$success = touch ( self::getLockFilePath () );
		if ($success) {
			Logger::debug ( "Lock file '" . self::getLockFilePath () . "' created." );
		} else {
			exit ( "Could not create lock file '" . self::getLockFilePath () . "'. Exiting..." );
		}
	}
	public static function release() {
		$success = unlink ( self::getLockFilePath () );
		if ($success) {
			Logger::debug ( "Lock file '" . self::getLockFilePath () . "' removed." );
		} else {
			Logger::error ( "Could not remove lock file '" . self::getLockFilePath () . "'. Exiting..." );
		}
	}
	private static function getLockFilePath() {
		// Get the main folder
		return dirname ( __DIR__ ) . self::LOCK_FILE_NAME;
	}
}
