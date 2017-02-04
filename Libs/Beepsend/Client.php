<?php

namespace Beepsend;

use Beepsend\Connector;
use Beepsend\Exception\NotFoundHelper;
use Beepsend\Exception\NotFoundResource;
use Beepsend\Request;

require_once __DIR__ . '/Connector/Curl.php';
require_once __DIR__ . '/Connector/Stream.php';
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Exception/NotFoundResource.php';
require_once __DIR__ . '/Exception/NotFoundHelper.php';
require_once __DIR__ . '/Resource/Message.php';
require_once __DIR__ . '/Exception/InvalidToken.php';

/**
 * Beepsend client
 * 
 * @package Beepsend
 */
class Client {
	
	/**
	 * Version of Beepsend PHP helper
	 */
	public $version = '1.0';
	
	/**
	 * Beepsend request handler
	 * 
	 * @var Beepsend\Request;
	 */
	private $request;
	
	/**
	 * Init beepsend client
	 * 
	 * @param string $token
	 *        	User or Connection token to work with
	 * @param object $connector
	 *        	Specify manually connector that you want to use
	 */
	public function __construct($token, $connector = null) {
		if (empty ( $token )) {
			/* User didn't set token */
			throw new InvalidToken ( 'Please set valid token!' );
		}
		
		if (! $connector) {
			/* Detect connector that we will use */
			if (extension_loaded ( 'curl' )) {
				$connector = new Connector\Curl ();
			} else {
				$connector = new Connector\Stream ();
			}
		}
		
		$this->request = new Request ( $token, $connector );
	}
	
	/**
	 * Load resource that user will work with
	 * 
	 * @param string $resource
	 *        	Name of resource that we want to load
	 * @return Object
	 */
	public function __get($resource) {
		return $this->loadResource ( $resource );
	}
	
	/**
	 * Load helper that user will work with
	 * 
	 * @param string $helper        	
	 */
	public function getHelper($helper) {
		return $this->loadHelper ( $helper );
	}
	
	/**
	 * Try to load resource
	 * 
	 * @param string $resource
	 *        	Resource name
	 * @return Object Resource
	 * @throws NotFoundResource
	 */
	private function loadResource($resource) {
		$resourceName = ucfirst ( $resource );
		if (file_exists ( __DIR__ . '/Resource/' . $resourceName . '.php' )) {
			$loadResource = "Beepsend\Resource\\{$resourceName}";
			return new $loadResource ( $this->request );
		}
		
		throw new NotFoundResource ( 'Resource ' . $resourceName . ' can not be found!' );
	}
	
	/**
	 * Try to load helper
	 * 
	 * @param string $helper
	 *        	Helper name
	 * @return Object Resource
	 * @throws NotFoundResource
	 */
	private function loadHelper($helper) {
		$helperName = ucfirst ( $helper );
		if (file_exists ( __DIR__ . '/Helper/' . $helperName . '.php' )) {
			$loadHelper = "Beepsend\Helper\\{$helperName}";
			return new $loadHelper ( $this->request );
		}
		
		throw new NotFoundHelper ( 'Helper ' . $helperName . ' can not be found!' );
	}
}