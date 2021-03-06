<?php

namespace Beepsend\Exception;

/**
 * Throw when we can't find helper to load
 * @package Beepsend
 */
final class NotFoundHelper extends \Exception
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
