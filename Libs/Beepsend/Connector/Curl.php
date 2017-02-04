<?php

namespace Beepsend\Connector;

use Beepsend\Connector\ConnectorInterface;
use Beepsend\Exception\ConnectionError;

require_once __DIR__ . '/ConnectorInterface.php';
require_once __DIR__ . '/../Exception/InvalidToken.php';

/**
 * Beepsend curl connector
 * 
 * @package Beepsend
 */
class Curl implements ConnectorInterface {
	/**
	 * Array of request headers
	 * 
	 * @var array
	 */
	private $headers;
	
	/**
	 * Curl holder
	 * 
	 * @var Object
	 */
	private $curl;
	
	/**
	 * Make some request over Beepsend API, supporting GET, POST, PUT and DELETE methods
	 * 
	 * @param string $url
	 *        	Beepsend API url that we are calling
	 * @param string $method
	 *        	Request method
	 * @param array $params
	 *        	Array of additional parameters
	 * @return array
	 */
	public function call($url, $method, $params) {
		if ($method == 'GET') {
			$url = $this->appendParamsToUrl ( $url, $params );
		} else {
			$this->addHeader ( 'Content-Type', 'application/json' );
			$this->addHeader ( 'Content-Length', strlen ( json_encode ( $params ) ) );
		}
		
		$this->makeConnection ( $url, $method, json_encode ( $params ) );
		
		$response = $this->makeRequest ();
		$info = $this->getCurlInfo ();
		$errors = $this->getCurlError ();
		
		if ($errors) {
			throw new ConnectionError ( $errors );
		}
		
		$this->closeConnection ();
		
		return array (
				'info' => $info,
				'response' => $response 
		);
	}
	
	/**
	 * Upload file to Beepsend API, currently supporting only POST method
	 * 
	 * @param string $url
	 *        	Beepsend API url that we are calling
	 * @param array $params
	 *        	Array of additional parameters
	 * @param string $rawData
	 *        	String using this for posting file content
	 * @return Beepsend\Response
	 */
	public function upload($url, $params, $rawData) {
		$this->addHeader ( 'Content-Type', 'application/x-www-form-urlencoded' );
		$this->makeConnection ( $url, 'POST', count ( $params ) > 0 ? $params : $rawData );
		
		$response = $this->makeRequest ();
		$info = $this->getCurlInfo ();
		$errors = $this->getCurlError ();
		
		if ($errors) {
			throw new ConnectionError ( $errors );
		}
		
		$this->closeConnection ();
		
		return array (
				'info' => $info,
				'response' => $response 
		);
	}
	
	/**
	 * Make connection to Beepsend API
	 * 
	 * @param string $url
	 *        	Beepsend API url that we are calling
	 * @param string $method
	 *        	Request method
	 * @param array $params
	 *        	Array of additional parameters
	 */
	private function makeConnection($url, $method, $params) {
		$options = array (
				CURLOPT_HEADER => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_BINARYTRANSFER => true 
		);
		
		if ($method !== 'GET') {
			$options [CURLOPT_POSTFIELDS] = $params;
		}
		
		if ($method == 'PUT' || $method == 'DELETE') {
			$options [CURLOPT_CUSTOMREQUEST] = $method;
		}
		
		$options [CURLOPT_HTTPHEADER] = $this->prepareHeaders ();
		
		$this->curl = curl_init ( $url );
		curl_setopt_array ( $this->curl, $options );
	}
	
	/**
	 * Make request to server
	 * 
	 * @return string
	 */
	private function makeRequest() {
		return curl_exec ( $this->curl );
	}
	
	/**
	 * Get curl info
	 * 
	 * @return array
	 */
	public function getCurlInfo() {
		return curl_getinfo ( $this->curl );
	}
	
	/**
	 * Get curl error message
	 * 
	 * @return string
	 */
	public function getCurlError() {
		return curl_error ( $this->curl );
	}
	
	/**
	 * Close curl conenction
	 */
	private function closeConnection() {
		curl_close ( $this->curl );
	}
	
	/**
	 * Add request header
	 * 
	 * @param string $name
	 *        	Name of header
	 * @param string $value
	 *        	Valud of header
	 */
	public function addHeader($name, $value) {
		$this->headers [$name] = $value;
	}
	
	/**
	 * Return headers for request
	 * 
	 * @return string
	 */
	private function prepareHeaders() {
		$header = array ();
		foreach ( $this->headers as $n => $v ) {
			$header [] = $n . ': ' . $v;
		}
		
		return $header;
	}
	
	/**
	 * Append parameters to url, using for GET request.
	 * 
	 * @param string $url
	 *        	Url that we will call
	 * @param array $params
	 *        	Array of parameters
	 * @return string
	 */
	private function appendParamsToUrl($url, $params = array()) {
		if (empty ( $params )) {
			return $url;
		}
		
		return $url . '?' . http_build_query ( $params );
	}
}