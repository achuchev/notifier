<?php

namespace Notifier\Incoming;

abstract class IncomingBase {
	protected $connectionData;
	public function __construct($connectionData) {
		$this->connectionData = $connectionData;
		$this->checkConnectionData ();
	}
	function __destruct() {
		$this->disconnect ();
	}
	abstract public function getNewMessageDataCount();
	abstract public function getNewMessageData();
	abstract protected function checkConnectionData();
	abstract protected function connect();
	abstract protected function disconnect();
}