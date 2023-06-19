<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Repository;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\Entity\BaseCommandLog;
use JmvDevelop\Domain\Entity\CommandLogInterface;

/**
 * CommandLogRepositoryInterface.
 */
interface CommandLogRepositoryInterface
{
    /** @param CommandLogInterface::TYPE_* $type */
    public function createEntity(CommandInterface $command, int $type, CommandLogInterface $previousCommandLog = null, \Throwable $exception = null): CommandLogInterface;

    /** @return string[] */
    public function getChoicesForCommandClass(): array;

    /** @return array<string, BaseCommandLog::TYPE_*> */
    public function getChoicesForType(): array;
}
