<?php

namespace JmvDevelop\Domain\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;

class UngrantedException extends DomainException
{
    #[Pure] public function __construct(string $message = "You aren't authorized to this action", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
