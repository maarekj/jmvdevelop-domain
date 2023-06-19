<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Exception;

use JetBrains\PhpStorm\Pure;
use JmvDevelop\Domain\CommandInterface;

class UnhandledException extends DomainException
{
    protected CommandInterface $command;

    #[Pure]
    public function __construct(CommandInterface $command, int $code = 0, \Exception $previous = null)
    {
        $message = sprintf('This command %s is unhandled.', $command::class);
        $this->command = $command;
        parent::__construct($message, false, $code, $previous);
    }

    public function getCommand(): CommandInterface
    {
        return $this->command;
    }
}
