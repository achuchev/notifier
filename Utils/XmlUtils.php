<?php

namespace Notifier\Utils;

use Notifier\Logging\Logger;

require_once __DIR__ . '/../Logging/Logger.php';
class XmlUtils {
	public static function LogXmlError($error, $xml, $suppressWarnings = True) {
		$message = $xml [$error->line - 1] . "\n";
		$message .= str_repeat ( '-', $error->column ) . "^\n";
		
		switch ($error->level) {
			case LIBXML_ERR_WARNING :
				$message .= "Warning $error->code: ";
				break;
			case LIBXML_ERR_ERROR :
				$message .= "Error $error->code: ";
				break;
			case LIBXML_ERR_FATAL :
				$message .= "Fatal Error $error->code: ";
				break;
		}
		
		$message .= trim ( $error->message ) . "\n  Line: $error->line" . "\n  Column: $error->column";
		
		if ($error->file) {
			$message .= "\n  File: $error->file";
		}
		
		if (($error->level == LIBXML_ERR_WARNING) && ($suppressWarnings === True)) {
			return;
		}
		Logger::error ( $message );
	}
}