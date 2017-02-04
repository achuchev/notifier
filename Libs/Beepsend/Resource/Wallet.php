<?php

namespace Beepsend\Resource;

use Beepsend\Request;

require_once __DIR__ . '/../Request.php';
/**
 * Beepsend wallet resource
 * 
 * @package Beepsend
 */
class Wallet {
	
	/**
	 * Beepsend request handler
	 * 
	 * @var Beepsend\Request
	 */
	private $request;
	
	/**
	 * Action to call
	 * 
	 * @var array
	 */
	private $actions = array (
			'wallets' => '/wallets/',
			'transactions' => '/transactions/',
			'transfer' => '/transfer/',
			'notifications' => '/emails/',
			'topup' => '/topup/paypal/' 
	);
	
	/**
	 * Init customer resource
	 * 
	 * @param Beepsend\Request $request        	
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * Get all wallets that are connected to this user
	 */
	public function all() {
		$response = $this->request->execute ( $this->actions ['wallets'], 'GET' );
		return $response;
	}
	
	/**
	 * Get wallet details
	 * 
	 * @param int $walletId
	 *        	Wallet id
	 * @return array
	 */
	public function get($walletId) {
		$response = $this->request->execute ( $this->actions ['wallets'] . $walletId, 'GET' );
		return $response;
	}
	
	/**
	 * Update wallet id
	 * 
	 * @param int $walletId
	 *        	Wallet id
	 * @param string $name
	 *        	Name of wallet
	 * @param int $notifyLimit
	 *        	Email notifiacation limit
	 * @return array
	 */
	public function update($walletId, $name = null, $notifyLimit = null) {
		$data = array ();
		
		if (! is_null ( $name )) {
			$data ['name'] = $name;
		}
		
		if (! is_null ( $notifyLimit )) {
			$data ['notify_limit'] = $notifyLimit;
		}
		
		$response = $this->request->execute ( $this->actions ['wallets'] . $walletId, 'PUT', $data );
		return $response;
	}
	
	/**
	 * Returns all transactions for wallet
	 * 
	 * @param int $walletId
	 *        	Wallet id
	 * @param string $sinceId
	 *        	Returns results more recent than the specified ID.
	 * @param string $maxId
	 *        	Returns results with an ID older than or equal to the specified ID.
	 * @param int $count
	 *        	How many objects to fetch. Maximum 200, default 200.
	 * @return array
	 */
	public function transactions($walletId, $sinceId = null, $maxId = null, $count = null) {
		$data = array ();
		
		if (! is_null ( $sinceId )) {
			$data ['since_id'] = $sinceId;
		}
		
		if (! is_null ( $maxId )) {
			$data ['max_id'] = $maxId;
		}
		
		if (! is_null ( $count )) {
			$data ['count'] = $count;
		}
		
		$response = $this->request->execute ( $this->actions ['wallets'] . $walletId . $this->actions ['transactions'], 'GET' );
		return $response;
	}
	
	/**
	 * Transfer credits between your connection wallets.
	 * 
	 * @param int $sourceId
	 *        	Source wallet
	 * @param int $targetId
	 *        	Target wallet
	 * @param float $amount
	 *        	Amount to transfer
	 * @return array
	 */
	public function transfer($sourceId, $targetId, $amount) {
		$data = array (
				'amount' => $amount 
		);
		
		$response = $this->request->execute ( $this->actions ['wallets'] . $sourceId . $this->actions ['transfer'] . $targetId . '/', 'POST', $data );
		return $response;
	}
	
	/**
	 * Add credit to wallet
	 * 
	 * @param int $walletId
	 *        	Wallet add that we want to add credit
	 * @param int $amount
	 *        	Amount of money that we wan't to add
	 * @param string $returnUrl
	 *        	The URL to redirect a user when a payment is complete and successful. Default: https://beepsend.com/success.html
	 * @param string $cancleUrl
	 *        	The URL to redirect a user when a payment is aborted. Default: https://beepsend.com/cancel.html
	 */
	public function topup($walletId, $amount, $returnUrl = null, $cancleUrl = null) {
		$data = array (
				'amount' => $amount 
		);
		
		if (! is_null ( $returnUrl )) {
			$data ['url'] ['return'] = $returnUrl;
		}
		
		if (! is_null ( $cancleUrl )) {
			$data ['url'] ['cacnle'] = $cancleUrl;
		}
		
		$response = $this->request->execute ( $this->actions ['wallets'] . $walletId . $this->actions ['topup'], 'POST', $data );
		return $response;
	}
	
	/**
	 * Get a list of your external emails.
	 * 
	 * @param int $walletId
	 *        	Wallet id
	 * @return array
	 */
	public function notifications($walletId) {
		$response = $this->request->execute ( $this->actions ['wallets'] . $walletId . $this->actions ['notifications'], 'GET' );
		return $response;
	}
	
	/**
	 * Add external email for notifications to wallet.
	 * 
	 * @param int $walletId
	 *        	Wallet id
	 * @param string $email
	 *        	Email address
	 * @return array
	 */
	public function addNotificationEmail($walletId, $email) {
		$data = array (
				'email' => $email 
		);
		
		$response = $this->request->execute ( $this->actions ['wallets'] . $walletId . $this->actions ['notifications'], 'POST', $data );
		return $response;
	}
	
	/**
	 * Delete external email for notifications to wallet.
	 * 
	 * @param int $walletId
	 *        	Wallet id
	 * @param int $emailId
	 *        	Email id
	 * @return array
	 */
	public function deleteNotificationEmail($walletId, $emailId) {
		$response = $this->request->execute ( $this->actions ['wallets'] . $walletId . $this->actions ['notifications'] . $emailId, 'DELETE' );
		return $response;
	}
}