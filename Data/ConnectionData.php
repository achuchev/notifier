<?php

namespace Notifier\Data;

class ConnectionData {
	public $hostname;
	public $username;
	public $password;
	public $folder;
	public function __construct($hostname=null, $username=null, $password=null, $folder=null) {
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->folder = $folder;
	}
}
