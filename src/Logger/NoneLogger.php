<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Logger;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\CommandLoggerInterface;

final class NoneLogger implements CommandLoggerInterface
{
    public function log(CommandInterface $command): array
    {
        return [];
    }
}
