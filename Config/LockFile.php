<?php

namespace Notifier\Config;

use Notifier\Logging\Logger;
use Notifier\Utils\Utils;

require_once __DIR__ . '/../Logging/Logger.php';
require_once __DIR__ . '/../Utils/Utils.php';
class LockFile {
	const LOCK_FILE_NAME = '/notifier.in.progress';
	const SLEEP_TIME = 5;
	private static $lockFilePath;
	public static function acquire_or_die() {
		$lockFileWaitSeconds = Config::getProperty ( "Program", "lockFileWaitSeconds" );
		while ( TRUE ) {
			// Clear the file stat cache to avoid problems
			clearstatcache ();
			if (! file_exists ( self::getLockFilePath () )) {
				// Lock file does not exist
				break;
			}
			// Check for old lock file
			$lock_file_ctime = filectime ( self::getLockFilePath () );
			if ($lock_file_ctime == FALSE) {
				// For some reason we cannot get the lock file time
				continue;
			}
			
			// Max lock time is twice the maxRunTime
			$max_lock_file_ctime = $lock_file_ctime + (2 * Config::getMaxRunTime ());
			$curTime = time ();
			if ($curTime >= $max_lock_file_ctime) {
				Logger::warning ( "Seems like the lock file is too old. Current time is '" . date ( "F d Y H:i:s", $curTime ) . "', file time is '" . date ( "F d Y H:i:s", $lock_file_ctime ) . "'. Deleting it." );
				self::release ();
				continue;
			}
			// Check if we have more time wait
			if ($lockFileWaitSeconds <= 0) {
				exit ( "Lock file '" . self::getLockFilePath () . "' exists. Exiting..." );
			}
			
			$lockFileWaitSeconds = $lockFileWaitSeconds - self::SLEEP_TIME;
			Logger::debug ( "Lock file still exists. Time left " . $lockFileWaitSeconds . " seconds." );
			sleep ( self::SLEEP_TIME );
		}
		$success = touch ( self::getLockFilePath () );
		if (! $success) {
			exit ( "Could not create lock file '" . self::getLockFilePath () . "'. Exiting..." );
		}
		Logger::debug ( "Lock file '" . self::getLockFilePath () . "' created." );
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
		if (Utils::IsNullOrEmptyString ( self::$lockFilePath )) {
			// Get the main folder and add the file name
			self::$lockFilePath = dirname ( __DIR__ ) . self::LOCK_FILE_NAME;
		}
		return self::$lockFilePath;
	}
}
