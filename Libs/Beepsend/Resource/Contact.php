<?php

namespace Beepsend\Resource;

use Beepsend\Exception\FileNotFound;
use Beepsend\Request;

require_once __DIR__ . '/../Request.php';
require_once __DIR__ . '/../Exception/FileNotFound.php';
/**
 * Beepsend contact resource
 *
 * @package Beepsend
 */
class Contact {
	
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
			'contacts' => '/contacts/',
			'groups' => '/contacts/groups/',
			'upload' => '/upload/' 
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
	 * Get all contacts belonging to your user
	 *
	 * @param string $group
	 *        	Group id or name
	 * @param string $options
	 *        	Array of additional options. More info on: http://api.beepsend.com/docs.html#contacts
	 * @return array
	 */
	public function all($group = null, $options = array()) {
		$data = array ();
		
		if (! is_null ( $group )) {
			$data ['group'] = $group;
		}
		
		/* Merge additional options if we have */
		if (! empty ( $options )) {
			$data = array_merge ( $data, $options );
		}
		
		$response = $this->request->execute ( $this->actions ['contacts'], 'GET', $data );
		return $response;
	}
	
	/**
	 * Add new contact
	 *
	 * @param string $msisdn
	 *        	Contact number
	 * @param string $firstName
	 *        	Contact first name
	 * @param string $lastName
	 *        	Contact last name
	 * @param int $groupId
	 *        	Contact group id
	 * @return array
	 */
	public function add($msisdn, $firstName = null, $lastName = null, $groups = null) {
		$data = array (
				'msisdn' => $msisdn 
		);
		
		if (! is_null ( $firstName )) {
			$data ['firstname'] = $firstName;
		}
		
		if (! is_null ( $lastName )) {
			$data ['lastname'] = $lastName;
		}
		
		if (! is_null ( $groups )) {
			if (! is_array ( $groups )) {
				throw new \InvalidArgumentException ( 'groups must be an array of integers' );
			} else {
				foreach ( $groups as $group ) {
					if (! is_int ( $group )) {
						throw new \InvalidArgumentException ( 'groups must be an array of integers.' );
					} else {
						$data ['groups'] [] = array (
								'id' => ( int ) $group 
						);
					}
				}
			}
		}
		
		$response = $this->request->execute ( $this->actions ['contacts'], 'POST', $data );
		return $response;
	}
	
	/**
	 * Update existing contact
	 *
	 * @param int $contactId
	 *        	Contact id
	 * @param array $options
	 *        	Array of options to update. Available keys: msisdn, firstname, lastname, group_id.
	 */
	public function update($contactId, $options) {
		$response = $this->request->execute ( $this->actions ['contacts'] . $contactId, 'PUT', $options );
		return $response;
	}
	
	/**
	 * Delete contact
	 *
	 * @param int $contactId
	 *        	Contact id
	 * @return array
	 */
	public function delete($contactId) {
		$response = $this->request->execute ( $this->actions ['contacts'] . $contactId, 'DELETE' );
		return $response;
	}
	
	/**
	 * Get all contact groups belonging to your user
	 *
	 * @param string $sinceId
	 *        	Returns results more recent than the specified ID.
	 * @param string $maxId
	 *        	Returns results with an ID older than or equal to the specified ID.
	 * @param int $count
	 *        	How many objects to fetch. Maximum 200, default 200.
	 * @return array
	 */
	public function groups($sinceId = null, $maxId = null, $count = null) {
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
		
		$response = $this->request->execute ( $this->actions ['groups'], 'GET', $data );
		return $response;
	}
	
	/**
	 * Get content of a contact group
	 *
	 * @param int $groupId
	 *        	Group id
	 * @return array
	 */
	public function group($groupId) {
		$response = $this->request->execute ( $this->actions ['groups'] . $groupId, 'GET' );
		return $response;
	}
	
	/**
	 * Add new contact group
	 *
	 * @param string $groupName
	 *        	Group id
	 * @return array
	 */
	public function addGroup($groupName) {
		$data = array (
				'name' => $groupName 
		);
		
		$response = $this->request->execute ( $this->actions ['groups'], 'POST', $data );
		return $response;
	}
	
	/**
	 * Update existing contact group
	 *
	 * @param int $groupId
	 *        	Group id
	 * @param string $groupName
	 *        	Group name
	 * @return array
	 */
	public function updateGroup($groupId, $groupName) {
		$data = array (
				'name' => $groupName 
		);
		
		$response = $this->request->execute ( $this->actions ['groups'] . $groupId, 'PUT', $data );
		return $response;
	}
	
	/**
	 * Delete contact group
	 *
	 * @param int $groupId        	
	 * @return array
	 */
	public function deleteGroup($groupId) {
		$response = $this->request->execute ( $this->actions ['groups'] . $groupId, 'DELETE' );
		return $response;
	}
	
	/**
	 * Upload contacts stored in a csv file to some group
	 *
	 * @param string $file
	 *        	Path to csv file
	 * @param int $groupId
	 *        	Group id
	 * @link http://api.beepsend.com/docs.html#contacts-upload More informations about file formating
	 * @return array
	 */
	public function upload($file, $groupId) {
		if (! file_exists ( $file )) {
			throw new FileNotFound ( 'File you are trying to upload doesn\'t exists!' );
		}
		
		/* Read file data */
		$data = file_get_contents ( $file );
		
		$response = $this->request->upload ( $this->actions ['groups'] . $groupId . $this->actions ['upload'], $data );
		return $response;
	}
}