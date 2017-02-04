<?php

namespace Beepsend;

/**
 * Beepsend response
 * @package Beepsend
 */
class Response 
{
    
    /**
     * Response content type
     * @var string
     */
    private $contentType;
    
    /**
     * Raw Response from Beepsend API
     * @var array
     */
    private $rawResponse;
    
    /**
     * Set file name for download file
     * @var string
     */
    private $fileName;
    
    /**
     * Init Beepsend response
     * @param string $rawResponse Json object from Beepsend API
     * @param array $info Curl info
     */
    public function __construct($rawResponse, $info)
    {
        $this->contentType = isset($info['content_type']) ? $info['content_type'] : $info['Content-Type'];
        $this->rawResponse = $rawResponse;
    }
    
    /**
     * Set file name to use when downloading some file
     * @param string $fileName
     * @return Beepsend\Response
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }
    
    /**
     * Return response data
     * @return array
     */
    public function get()
    {
        switch ($this->contentType) {
            case 'application/json':
                return $this->parseResponse($this->rawResponse);
            case 'text/csv':
            case 'text/octect-stream':
                return $this->downloadCsv($this->fileName);
        }
    }
    
    /**
     * Download CSV file from response and set proper headers
     */
    private function downloadCsv($fileName)
    {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $this->rawResponse;
    }
    
    /**
     * Parse raw response from Beepsend API
     * @param string $rawResponse Json object
     * @return array
     */
    private function parseResponse($rawResponse)
    {
        return json_decode($rawResponse, true);
    }
    
}
