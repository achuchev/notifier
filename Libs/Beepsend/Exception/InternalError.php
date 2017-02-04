<?php

namespace Beepsend\Exception;

/**
 * Throw when API returns internal error
 * @package Beepsend
 */
final class InternalError extends \Exception
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
