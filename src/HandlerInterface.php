<?php

declare(strict_types=1);

namespace JmvDevelop\Domain;

interface HandlerInterface
{
    /** @return bool Return true if this handler accept the command */
    public function acceptCommand(CommandInterface $command): bool;

    public function handle(CommandInterface $command): void;
}
