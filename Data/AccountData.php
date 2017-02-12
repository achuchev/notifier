<?php

namespace Notifier\Data;

class AccountData {
	public $paradoxSite;
	public $connectionDataSMS;
	public $connectionDataVoice;
	public $connectionDataEmail;
	public function __construct($paradoxSite, $connectionDataSMS = NULL, $connectionDataVoice = NULL, $connectionDataEmail = NULL) {
		$this->paradoxSite = $paradoxSite;
		$this->connectionDataSMS = $connectionDataSMS;
		$this->connectionDataVoice = $connectionDataVoice;
		$this->connectionDataEmail = $connectionDataEmail;
	}
}