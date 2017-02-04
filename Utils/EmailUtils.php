<?php

namespace Notifier\Utils;

class EmailUtils {
	const MAX_SMS_MESSAGE_LENGTH = 160;
	public static function simplifyEmailBody($body, $maxBodyLength) {
		// replace newlines with space
		$body = preg_replace ( "/(\r?\n)/", " ", $body );
		
		// according to the SMTP RFC the max characters per line is 70
		$body = wordwrap ( $body, 70, "\r\n" );
		
		// split the source body to part depends on the max body length
		$parts = str_split ( $body, $split_length = $maxBodyLength );
		
		// trim whitespaces
		foreach ( $parts as $part ) {
			$part = trim ( $part );
		}
		return $parts;
	}
	public static function simplifyEmailAddress($emailAddress) {
		$emails = array ();
		if (preg_match_all ( '/\s*"?([^><,"]+)"?\s*((?:<[^><,]+>)?)\s*/', $emailAddress, $matches, PREG_SET_ORDER ) > 0) {
			foreach ( $matches as $m ) {
				if (! empty ( $m [2] )) {
					$emails [trim ( $m [2], '<>' )] = $m [1];
				} else {
					$emails [$m [1]] = '';
				}
			}
		}
		if (count ( $emails ) == 1) {
			reset ( $emails );
			return key ( $emails );
		}
		return $emails;
	}
	public static function getMaxBodyLength($subject, $sender) {
		return self::MAX_SMS_MESSAGE_LENGTH - strlen ( "Subject: " . $subject . " From: " . $sender . " Text: " );
	}
}