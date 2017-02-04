<?php

namespace Beepsend\Resource;

use Beepsend\Request;

require_once __DIR__ . '/../Request.php';

/**
 * Class Error
 * 
 * @package Beepsend\Resource
 */
class Error {
	/**
	 * Beepsend request handler
	 * 
	 * @var \Beepsend\Request
	 */
	private $request;
	
	/**
	 * Actions to call
	 * 
	 * @var array
	 */
	private $actions = array (
			'errors' => '/errors/' 
	);
	
	/**
	 * Init error resource
	 * 
	 * @param \Beepsend\Request $request        	
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * Get all errors
	 * 
	 * @return array
	 */
	public function all() {
		$response = $this->request->execute ( $this->actions ['errors'], 'GET' );
		return $response;
	}
	
	/**
	 * Get data for single error code
	 * 
	 * @param string $id
	 *        	Error id
	 * @return array
	 */
	public function get($id = '') {
		$response = $this->request->execute ( $this->actions ['errors'] . $id, 'GET' );
		return $response;
	}
}