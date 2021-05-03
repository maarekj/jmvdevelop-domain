<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Tests\Fixtures;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\HandlerInterface;

class Command1Handler implements HandlerInterface
{
    public function acceptCommand(CommandInterface $command): bool
    {
        return $command instanceof Command1;
    }

    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof Command1) {
            throw new \InvalidArgumentException('invalid argument');
        }

        $command->setReturnValue(true);
    }
}
