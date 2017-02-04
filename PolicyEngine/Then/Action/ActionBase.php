<?php

namespace Notifier\PolicyEngine\Then\Action;

use Notifier\Data\MessageSeverity;

require_once __DIR__ . '/../../../Data/MessageSeverity.php';
abstract class ActionBase {
	protected $connectionData;
	public function __construct($connectionData) {
		$this->connectionData = $connectionData;
	}
	abstract public function notify($messageData, $notifySeverity = MessageSeverity::Medium);
}