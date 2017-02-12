<?php

namespace Notifier\Data;

require_once __DIR__ . '/MessageSeverity.php';
class MessageData {
	public $sender;
	public $recipient;
	public $subject;
	public $body;
	public $severity;
	public $bodyRich;
	public $accountData;
	public function __construct($sender, $recipient, $subject, $body, $severity = MessageSeverity::Medium, $bodyRich = NULL, $accountData = NULL) {
		$this->sender = $sender;
		$this->recipient = $recipient;
		$this->subject = $subject;
		$this->body = $body;
		$this->severity = $severity;
		$this->bodyRich = $bodyRich;
		$this->accountData = $accountData;
	}
}