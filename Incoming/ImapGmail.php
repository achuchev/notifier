<?php

namespace Notifier\Incoming;

use Notifier\Data\MessageData;
use Notifier\Incoming\IncomingBase;
use Notifier\Logging\Logger;
use Notifier\Utils\Utils;
use Notifier\Config\Config;

require_once __DIR__ . '/IncomingBase.php';
require_once __DIR__ . '/../Data/MessageData.php';
require_once __DIR__ . '/../Logging/Logger.php';
require_once __DIR__ . '/../Utils/Utils.php';
class ImapGmail extends IncomingBase {
	const GMAIL_IMAP_SERVER = "imap.gmail.com";
	const GMAIL_IMAP_SERVER_PORT = "993";
	private $imapFolder = NULL;
	private $isConnected = FALSE;
	public function getNewMessageDataCount() {
		Logger::info ( "Getting New Message Data count from GMAIL account " . $this->connectionData->username );
		$unseenMessagesCount = 0;
		try {
			$this->connect ();
			
			// grab 'UNSEEN' messages only
			$emails = imap_search ( $this->imapFolder, 'UNSEEN' );
			$unseenMessagesCount = count ( $emails );
			Logger::debug ( "New messages count: " . $unseenMessagesCount );
		} catch ( Exception $ex ) {
			// TODO: handle the exception
			Logger::error ( 'Could not process IMAP messages. Error: ', $ex . getMessage () );
			$this->disconnect ();
		}
		return $unseenMessagesCount;
	}
	public function getNewMessageData() {
		Logger::info ( "Getting New Message Data from GMAIL account " . $this->connectionData->username );
		
		$messageDataList = array ();
		try {
			$this->connect ();
			$emails = $this->getUnseenMessages ();
			
			if ($emails) {
				$output = '';
				
				// put the newest emails on top
				rsort ( $emails );
				foreach ( $emails as $emailNumber ) {
					// get information specific to this email
					$overview = imap_fetch_overview ( $this->imapFolder, $emailNumber, 0 );
					$sender = $overview [0]->from;
					$recipient = $overview [0]->to;
					$subject = $overview [0]->subject;
					$body = $this->getPlainMessageBody ( $this->imapFolder, $emailNumber );
					
					Logger::debug ( "\r\n\tSender: " . $sender . "\r\n\tRecipient: " . $recipient . "\r\n\tSubject: " . $subject . "\r\n\tBody: " . $body );
					
					$messageData = new MessageData ( $sender, $recipient, $subject, $body );
					array_push ( $messageDataList, $messageData );
				}
			}
		} catch ( Exception $error ) {
			Logger::error ( 'Could not process IMAP messages. Error: ', $error->getMessage () . " " . imap_last_error () );
			// We assume the in the next round the code will connect again
			$this->disconnect ();
		}
		return $messageDataList;
	}
	private function getUnseenMessages() {
		// grab only 'UNSEEN' messages only
		return imap_search ( $this->imapFolder, 'UNSEEN' );
	}
	private function getPlainMessageBody($imapFolder, $emailNumber) {
		$imapOptions = null;
		if (Config::isTestMode () == TRUE) {
			$imapOptions = FT_PEEK;
		}
		// Multipart/Alternative MIME message. The TEXT/PLAIN is "1.1"
		$plainMessageBody = imap_fetchbody ( $imapFolder, $emailNumber, 1.1, $imapOptions );
		if (Utils::IsNullOrEmptyString ( $plainMessageBody )) {
			// Not a MIME message. The body is "1"
			$plainMessageBody = imap_fetchbody ( $imapFolder, $emailNumber, 1, $imapOptions );
		}
		return $plainMessageBody;
	}
	protected function checkConnectionData() {
		if (Utils::IsNullOrEmptyString ( $this->connectionData->folder )) {
			$this->connectionData->folder = "INBOX";
		}
		
		if (Utils::IsNullOrEmptyString ( $this->connectionData->hostname )) {
			$this->connectionData->hostname = self::GMAIL_IMAP_SERVER;
		}
	}
	protected function connect() {
		Logger::debug ( "Connecting to Gmail..." );
		if ($this->isConnected == TRUE) {
			Logger::debug ( "We are alredy connected." );
			return;
		}
		// try to connect
		$this->imapFolder = imap_open ( "{" . $this->connectionData->hostname . ":" . self::GMAIL_IMAP_SERVER_PORT . "/imap/ssl}" . $this->connectionData->folder, $this->connectionData->username, $this->connectionData->password );
		if ($this->imapFolder == FALSE) {
			throw new \Exception ( "Could not connect to GMAIL. Error: " . imap_last_error () );
		}
		$this->isConnected = TRUE;
		
		Logger::debug ( "Connected to Gmail." );
	}
	protected function disconnect() {
		Logger::debug ( "Disconnecting from GMAIL..." );
		if ($this->isConnected == FALSE) {
			Logger::debug ( "Not connected to GMAIL. Nothing to do." );
			return;
		}
		// close the connection
		imap_close ( $this->imapFolder );
		Logger::debug ( "Disconnected from GMAIL." );
	}
}