<?php

namespace JmvDevelop\Domain\Tests\Fixtures;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\HandlerInterface;

class Command2Handler implements HandlerInterface
{
    public function acceptCommand(CommandInterface $command): bool
    {
        return $command instanceof Command2;
    }

    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof Command2) {
            throw new \InvalidArgumentException("invalid argument");
        }

        $command->setReturnValue(true);
    }
}
