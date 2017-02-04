<?php

namespace Beepsend\Resource;

use Beepsend\Request;

require_once __DIR__ . '/../Request.php';
/**
 * Beepsend HLR resource
 * 
 * @package Beepsend
 */
class Hlr {
	
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
			'hlr' => '/hlr/',
			'validate' => '/hlr/validate/' 
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
	 * Run intermediate HLR call
	 * 
	 * @param int $msisdn
	 *        	Msisdn that we are looking HLR request
	 * @param string $connection
	 *        	Connection id
	 * @return array
	 */
	public function intermediate($msisdn, $connection = 'me') {
		$response = $this->request->execute ( $this->actions ['hlr'] . $msisdn, 'GET', array (
				'connection' => $connection 
		) );
		return $response;
	}
	
	/**
	 * Run bulk HLR call, will receive result to your connection's specified DLR
	 * 
	 * @param array $msisdns
	 *        	Array of msisdns
	 * @return array
	 */
	public function bulk($msisdns) {
		$response = $this->request->execute ( $this->actions ['hlr'], 'POST', array (
				'msisdn' => $msisdns 
		) );
		return $response;
	}
	
	/**
	 * Validate HLR request
	 * 
	 * @param int $msisdn
	 *        	Msisdn that we are looking HLR request
	 * @param string $connection
	 *        	Connection id
	 * @return array
	 */
	public function validate($msisdn) {
		$response = $this->request->execute ( $this->actions ['validate'], 'POST', array (
				'msisdn' => $msisdn 
		) );
		return $response;
	}
}