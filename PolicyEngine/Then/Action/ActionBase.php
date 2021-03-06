<?php

namespace Notifier\PolicyEngine\Then\Action;

use Notifier\Data\MessageSeverity;

require_once __DIR__ . '/../../../Data/MessageSeverity.php';
abstract class ActionBase {
	public function __construct() {
	}
	abstract public function notify($messageData, $notifySeverity = MessageSeverity::Medium);
}