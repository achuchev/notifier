<?php

namespace Beepsend\Exception;

/**
 * Throw when we hit rate limit
 * @package Beepsend
 */
final class RateLimit extends \Exception
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
