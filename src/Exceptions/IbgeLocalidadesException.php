<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Exceptions;

use Exception;

/**
 * Exception base para todas as exceptions da biblioteca
 */
class IbgeLocalidadesException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}