<?php

declare(strict_types=1);

namespace JmvDevelop\Domain;

interface CommandLoggerInterface
{
    /** @return array<string, mixed> */
    public function log(CommandInterface $command): array;
}
