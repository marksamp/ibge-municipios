<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Exceptions;

class IbgeApiException extends IbgeLocalidadesException
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}