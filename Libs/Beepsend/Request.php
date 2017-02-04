<?php

namespace Beepsend;

use Beepsend\Exception\InternalError;
use Beepsend\Exception\InvalidRequest;
use Beepsend\Exception\InvalidToken;
use Beepsend\Exception\NotFound;
use Beepsend\Exception\RateLimit;
use Beepsend\Response;

require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/Exception/InvalidToken.php';
require_once __DIR__ . '/Exception/InvalidRequest.php';
require_once __DIR__ . '/Exception/NotFound.php';
require_once __DIR__ . '/Exception/InternalError.php';
require_once __DIR__ . '/Exception/RateLimit.php';

/**
 * Beepsend request
 *
 * @package Beepsend
 */
class Request {
	
	/**
	 * Beepsend user or connection token that we are using to authorize on Beepsend API
	 *
	 * @var string
	 */
	private $token;
	
	/**
	 * Beepsend API version
	 *
	 * @var int
	 */
	private $version = 2;
	
	/**
	 * Beepsend PHP library user agent
	 *
	 * @var string
	 */
	private $userAgent = 'beepsend-php-sdk-v1.0';
	
	/**
	 * Beepsend API url
	 *
	 * @var string
	 */
	private $baseApiUrl = 'https://api.beepsend.com';
	
	/**
	 * Connector that we will use to communicate with API
	 *
	 * @var Object
	 */
	private $connector;
	
	/**
	 * Set requred values for Beepsend request
	 *
	 * @param string $token
	 *        	Token to work with
	 * @param object $connector
	 *        	Connector that we will use
	 * @throws InvalidToken
	 */
	public function __construct($token, $connector) {
		$this->token = $token;
		$this->connector = $connector;
	}
	
	/**
	 * Make some request over Beepsend API, supporting GET, POST, PUT and DELETE methods
	 *
	 * @param string $action
	 *        	Action that we are calling
	 * @param string $method
	 *        	Request method
	 * @param array $params
	 *        	Array of additional parameters
	 * @return Beepsend\Response
	 */
	public function execute($action, $method = 'GET', $params = array()) {
		$this->connector->addHeader ( 'User-Agent', $this->userAgent );
		$this->connector->addHeader ( 'Authorization', 'Token ' . $this->token );
		
		$rawResponse = $this->connector->call ( $this->baseApiUrl . '/' . $this->version . $action, $method, $params );
		return $this->response ( $rawResponse ['info'], $rawResponse ['response'] )->get ();
	}
	
	/**
	 * Make request over Beepsend API to download some file
	 *
	 * @param string $fileName
	 *        	Name of file
	 * @param string $action
	 *        	Action that we are calling
	 * @param string $method
	 *        	Request method
	 * @param array $params
	 *        	Array of additional parameters
	 * @return Beepsend\Response
	 */
	public function download($fileName, $action, $method = 'GET', $params = array()) {
		$this->connector->addHeader ( 'User-Agent', $this->userAgent );
		$this->connector->addHeader ( 'Authorization', 'Token ' . $this->token );
		
		$rawResponse = $this->connector->call ( $this->baseApiUrl . '/' . $this->version . $action, $method, $params );
		return $this->response ( $rawResponse ['info'], $rawResponse ['response'] )->setFileName ( $fileName )->get ();
	}
	
	/**
	 * Upload file to Beepsend API, currently supporting only POST method
	 *
	 * @param string $action
	 *        	Action that we are calling
	 * @param array $params
	 *        	Array of additional parameters
	 * @param string $rawData
	 *        	String using this for posting file content
	 * @return Beepsend\Response
	 */
	public function upload($action, $params = array(), $rawData = '') {
		$this->connector->addHeader ( 'User-Agent', $this->userAgent );
		$this->connector->addHeader ( 'Authorization', 'Token ' . $this->token );
		
		$rawResponse = $this->connector->upload ( $this->baseApiUrl . '/' . $this->version . $action, $params, $rawData );
		return $this->response ( $rawResponse ['info'], $rawResponse ['response'] )->get ();
	}
	
	/**
	 * Return Beepsend API version
	 *
	 * @return int
	 */
	public function getVersion() {
		return $this->version;
	}
	
	/**
	 * Return library user agent
	 *
	 * @return string
	 */
	public function getUserAgent() {
		return $this->userAgent;
	}
	
	/**
	 * Return Beepsend base API url
	 *
	 * @return string
	 */
	public function getBaseApiUrl() {
		return $this->baseApiUrl;
	}
	
	/**
	 * Return valid response based on respond from Beepsend API url
	 *
	 * @param array $info
	 *        	Curl info
	 * @param string $response
	 *        	Raw response from Beepsend API
	 * @return Beepsend\Response
	 * @throws InvalidToken
	 * @throws InvalidRequest
	 * @throws NotFound
	 * @throws InternalError
	 */
	private function response($info, $response) {
		switch ($info ['http_code']) {
			case 200 :
			case 201 :
			case 204 :
				return new Response ( $response, $info );
			case 401 :
				throw new InvalidToken ( 'A valid user API-token is required.' );
			case 403 :
				throw new InvalidRequest ( $this->parseError ( $response ) );
			case 404 :
				throw new NotFound ( 'Call you are looking for not existing, this means that something is wrong with API or this SDK.' );
			case 500 :
				throw new InternalError ( 'Something is wrong with API, please try again later.' );
			case 503 :
				throw new RateLimit ( 'Too many requests. Please try again later.' );
		}
	}
	
	/**
	 * Parse raw response
	 *
	 * @param string $rawResponse        	
	 * @return string
	 */
	private function parseError($rawResponse) {
		$errors = [ ];
		$response = json_decode ( $rawResponse, true );
		if (isset ( $response ['errors'] )) {
			$errors = $response ['errors'];
		} elseif (isset ( $response [0] ['errors'] )) {
			$errors = $response [0] ['errors'];
		} elseif (isset ( $response [0] ['description'] )) {
			$errors [] = $response [0] ['description'];
		} elseif (empty ( $errors )) {
			$errors = [ 
					'description' => 'There was an unidentified error with your request' 
			];
		}
		
		return $this->joinErrros ( $errors );
	}
	
	/**
	 * Create one string from array of errors
	 *
	 * @param array $errors        	
	 * @return string
	 */
	private function joinErrros($errors) {
		$response = null;
		
		foreach ( $errors as $error ) {
			$code = isset ( $error ['code'] ) ? 'Code: ' . $error ['code'] . ' ' : null;
			$description = isset ( $error ['description'] ) ? $error ['description'] : $error;
			$response .= $code . $description;
		}
		
		return $response;
	}
}
