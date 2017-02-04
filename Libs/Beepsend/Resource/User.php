<?php

namespace Beepsend\Resource;

use Beepsend\Request;

require_once __DIR__ . '/../Request.php';
/**
 * Beepsend user resource
 * 
 * @package Beepsend
 */
class User {
	
	/**
	 * Beepsend request handler
	 * 
	 * @var Beepsend\Request
	 */
	private $request;
	
	/**
	 * User that we are using for working with.
	 * Currently API only support working with logged in user.
	 * 
	 * @var string
	 */
	private $user = 'me';
	
	/**
	 * Actions to call
	 * 
	 * @var array
	 */
	private $actions = array (
			'users' => '/users/',
			'email' => 'email',
			'phone' => 'phone',
			'password' => 'password',
			'passwordreset' => 'passwordreset',
			'tokenreset' => '/tokenreset' 
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
	 * Get user details
	 * 
	 * @return array
	 */
	public function get() {
		$response = $this->request->execute ( $this->actions ['users'] . $this->user, 'GET' );
		return $response;
	}
	
	/**
	 * Update current user
	 * 
	 * @param array $options
	 *        	Array of options that we wan't to update.
	 * @link http://api.beepsend.com/docs.html#user-update List if all options we can update
	 * @return array
	 */
	public function update($options) {
		$response = $this->request->execute ( $this->actions ['users'] . $this->user, 'PUT', $options );
		return $response;
	}
	
	/**
	 * Update user email
	 * Password is needed for extra security
	 * 
	 * @param string $email
	 *        	Your new email address
	 * @param string $password
	 *        	Current password
	 * @return array
	 */
	public function updateEmail($email, $password) {
		$data = array (
				'email' => $email,
				'password' => $password 
		);
		
		$response = $this->request->execute ( $this->actions ['users'] . $this->user . '/' . $this->actions ['email'], 'PUT', $data );
		return $response;
	}
	
	/**
	 * Update user password
	 * 
	 * @param string $newPassword
	 *        	New password that you want to set
	 * @param string $oldPassword
	 *        	User current password, needed for extra security.
	 * @return array
	 */
	public function updatePassword($newPassword, $oldPassword) {
		$data = array (
				'password' => $oldPassword,
				'new_password' => $newPassword 
		);
		
		$response = $this->request->execute ( $this->actions ['users'] . $this->user . '/' . $this->actions ['password'], 'PUT', $data );
		return $response;
	}
	
	/**
	 * Reset user password, beepsend will send you email
	 * 
	 * @param string $email
	 *        	Your login email
	 * @return array
	 */
	public function resetUserPassword($email) {
		$data = array (
				'email' => $email 
		);
		
		$response = $this->request->execute ( $this->actions ['users'] . $this->actions ['passwordreset'], 'GET', $data );
		return $response;
	}
	
	/**
	 * You can set new password with reset hash
	 * 
	 * @param string $hash
	 *        	Hash that was sent to you via email
	 * @param password $password
	 *        	Password you want to set
	 * @return array
	 */
	public function setNewPassword($hash, $password) {
		$data = array (
				'password' => $password 
		);
		
		$response = $this->request->execute ( $this->actions ['users'] . $this->actions ['password'] . '/' . $hash, 'PUT', $data );
		return $response;
	}
	
	/**
	 * Reset user token, using your current password for extra security
	 * 
	 * @param string $password
	 *        	Current password
	 * @return array
	 */
	public function resetUserToken($password) {
		$data = array (
				'password' => $password 
		);
		
		$response = $this->request->execute ( $this->actions ['users'] . $this->user . $this->actions ['tokenreset'], 'GET', $data );
		return $response;
	}
	
	/**
	 * An email will be sent out after changing your email address asking you to verify that you have indeed changed it.
	 * Use the unique hash in the verification link to perform the verification.
	 * 
	 * @param string $hash        	
	 * @return array
	 */
	public function verifyEmail($hash) {
		$response = $this->request->execute ( $this->actions ['users'] . $this->actions ['email'] . '/' . $hash, 'GET' );
		return $response;
	}
	
	/**
	 * After changing your phone number.
	 * An SMS will be sent out asking you to verify the change. Use the unique hash to verify.
	 * 
	 * @param string $hash        	
	 * @return array
	 */
	public function verifyPhone($hash) {
		$response = $this->request->execute ( $this->actions ['users'] . $this->actions ['phone'] . '/' . $hash, 'GET' );
		return $response;
	}
}