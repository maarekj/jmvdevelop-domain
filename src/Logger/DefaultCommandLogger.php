<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Logger;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\CommandLoggerInterface;
use JmvDevelop\Domain\Utils\LoggerUtils;

class DefaultCommandLogger implements CommandLoggerInterface
{
    public function __construct(
        private LoggerUtils $loggerUtils
    ) {
    }

    public function log(CommandInterface $command): array
    {
        return $this->loggerUtils->logCommand($command);
    }
}
