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
class Gmail extends IncomingBase {
	const GMAIL_IMAP_SERVER = "imap.gmail.com";
	const GMAIL_IMAP_SERVER_PORT = "993";
	private $imapFolder = NULL;
	private $isConnected = FALSE;
	public function getNewMessageDataCount() {
		Logger::info ( "Getting New Message Data count from GMAIL account " . $this->connectionData->username );
		
		$username = urlencode ( $this->connectionData->username );
		$handle = curl_init ();
		$options = array (
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_HEADER => FALSE,
				CURLOPT_FOLLOWLOCATION => FALSE,
				CURLOPT_SSL_VERIFYHOST => '0',
				CURLOPT_SSL_VERIFYPEER => '1',
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
				CURLOPT_VERBOSE => FALSE,
				CURLOPT_URL => 'https://' . $username . ':' . $this->connectionData->password . '@mail.google.com/mail/feed/atom/' . $this->connectionData->folder 
		);
		
		curl_setopt_array ( $handle, $options );
		$output = ( string ) curl_exec ( $handle );
		$xml = simplexml_load_string ( $output );
		if (curl_errno ( $handle )) {
			Logger::error ( 'Could not retrive the count of new messages. Error: ' . curl_error ( $handle ) );
		}
		curl_close ( $handle );
		
		// Count the new messages
		$unseenMessagesCount = 0;
		foreach ( $xml->entry as $entry ) {
			Logger::debug ( "New message with subject '" . ( string ) $entry->title . "' counted." );
			$unseenMessagesCount ++;
		}
		
		if ($unseenMessagesCount > 0) {
			Logger::debug ( "The count of new messages is " . $unseenMessagesCount );
		} else {
			Logger::debug ( "No new messages found." );
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
		if ($this->isConnected == FALSE) {
			return;
		}
		Logger::debug ( "Disconnecting from GMAIL..." );
		// close the connection
		imap_close ( $this->imapFolder );
		Logger::debug ( "Disconnected from GMAIL." );
	}
}