<?php

namespace Beepsend\Resource;

use Beepsend\Request;

require_once __DIR__ . '/../Request.php';

/**
 * Beepsend customer resource
 * 
 * @package Beepsend
 */
class Customer {
	
	/**
	 * Beepsend request handler
	 * 
	 * @var Beepsend\Request
	 */
	private $request;
	
	/**
	 * Actions to call
	 * 
	 * @var array
	 */
	private $actions = array (
			'data' => '/customer/' 
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
	 * Get customer data
	 * 
	 * @return array
	 */
	public function get() {
		$response = $this->request->execute ( $this->actions ['data'], 'GET' );
		return $response;
	}
}