<?php

declare(strict_types=1);

namespace JmvDevelop\Domain;

interface CommandLoggerInterface
{
    public function log(CommandInterface $command): array;
}
