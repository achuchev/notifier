<?php

namespace Beepsend\Exception;

/**
 * Throw when we can't find API call we are looking for
 * @package Beepsend
 */
final class NotFound extends \Exception
{
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
