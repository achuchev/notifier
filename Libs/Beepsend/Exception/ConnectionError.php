<?php

namespace Beepsend\Exception;

/**
 * Throw when we can't connect to api service
 * @package Beepsend
 */
class ConnectionError extends \Exception {
    
    /**
     * @internal
     * @param string $message
     * @param integer $code
     * @param Object $previous
     */
    public function __construct($message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}
