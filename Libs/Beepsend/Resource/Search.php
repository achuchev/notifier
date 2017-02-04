<?php

namespace Beepsend\Resource;

use Beepsend\Request;

require_once __DIR__ . '/../Request.php';
/**
 * Beepsend search resource
 * 
 * @package Beepsend
 */
class Search {
	
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
			'contacts' => '/search/contacts/',
			'groups' => '/search/contact_groups/' 
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
	 * Search for contacts
	 * 
	 * @param string $query
	 *        	Will search entries matching on id, msisdn, firstname and lastname
	 * @param int $groupId
	 *        	Group id
	 * @return array
	 */
	public function contacts($query, $groupId = null) {
		$data = array (
				'query' => $query 
		);
		
		if (! is_null ( $groupId )) {
			$data ['group_id'] = $groupId;
		}
		
		$response = $this->request->execute ( $this->actions ['contacts'], 'GET', $data );
		return $response;
	}
	
	/**
	 * Search for groups
	 * 
	 * @param string $query
	 *        	Will search entries with matching name
	 * @return array
	 */
	public function groups($query) {
		$data = array (
				'query' => $query 
		);
		
		$response = $this->request->execute ( $this->actions ['groups'], 'GET', $data );
		return $response;
	}
}