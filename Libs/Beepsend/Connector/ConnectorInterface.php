<?php

namespace Beepsend\Connector;

/**
 * Connector Interface
 * @package Beepsend
 */
interface ConnectorInterface 
{
    /**
     * Method that will execute some call to Beepsend API
     * @param string $action Action that we are calling
     * @param string $method Request method
     * @param array $params Array of additional parameters
     * @return Beepsend\Response
     * @throws NotFound
     * @throws InvalidRequest
     */
    public function call($action, $method, $params);
    
    /**
     * Method that will upload some file
     * @param string $action Action that we are calling
     * @param array $params Array of additional parameters
     * @param string $rawData String using this for posting file content
     * @return Beepsend\Response
     * @throws NotFound
     * @throws InvalidRequest
     * @throws InvalidToken
     */
    public function upload($action, $params, $rawData);
    
    /**
     * Add new request header to connector
     * @param string $name Name of header
     * @param string $value Value of header
     */
    public function addHeader($name, $value);
    
}