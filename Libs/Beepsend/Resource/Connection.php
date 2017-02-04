<?php

namespace Beepsend\Resource;

use Beepsend\Request;

require_once __DIR__ . '/../Request.php';

/**
 * Beepsend connection resource
 * 
 * @package Beepsend
 */
class Connection {
	
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
			'connections' => '/connections/',
			'tokenreset' => '/tokenreset',
			'passwordreset' => '/passwordreset',
			'numbers' => '/numbers/' 
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
	 * Get all connections
	 * 
	 * @return array
	 */
	public function all() {
		$response = $this->request->execute ( $this->actions ['connections'], 'GET' );
		return $response;
	}
	
	/**
	 * Get data for single connection
	 * 
	 * @param string $connection
	 *        	Connection id
	 * @return array
	 */
	public function get($connection = 'me') {
		$response = $this->request->execute ( $this->actions ['connections'] . $connection, 'GET' );
		return $response;
	}
	
	/**
	 * Update connection data
	 * 
	 * @param string $connection
	 *        	Connection id
	 * @param array $options
	 *        	Option that we want to update
	 * @return array
	 */
	public function update($connection = 'me', $options = array()) {
		$response = $this->request->execute ( $this->actions ['connections'] . $connection, 'PUT', $options );
		return $response;
	}
	
	/**
	 * Reset connection token, need to use user token to perform this action
	 * 
	 * @param string $connection
	 *        	Connection id
	 * @return array
	 */
	public function resetToken($connection = 'me') {
		$response = $this->request->execute ( $this->actions ['connections'] . $connection . $this->actions ['tokenreset'], 'GET' );
		return $response;
	}
	
	/**
	 * Reset connection password, need to use user token to perform this action
	 * 
	 * @param string $connection
	 *        	Connection id
	 * @return array
	 */
	public function resetPassword($connection = 'me') {
		$response = $this->request->execute ( $this->actions ['connections'] . $connection . $this->actions ['passwordreset'], 'GET' );
		return $response;
	}
	
	/**
	 * Recive list of recipient numbers which can receive mobile originated messages
	 * 
	 * @return array
	 */
	public function recipientNumbers() {
		$response = $this->request->execute ( $this->actions ['numbers'], 'GET' );
		return $response;
	}
}