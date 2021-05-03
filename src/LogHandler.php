<?php

declare(strict_types=1);

namespace JmvDevelop\Domain;

use Doctrine\ORM\EntityManagerInterface;
use JmvDevelop\Domain\Entity\CommandLogInterface;
use JmvDevelop\Domain\Logger\CommandLogger;
use JmvDevelop\Domain\Repository\CommandLogRepositoryInterface;
use Psr\Log\LoggerInterface;

final class LogHandler implements HandlerInterface
{
    private ?HandlerInterface $decorated = null;

    public function __construct(
        private EntityManagerInterface $manager,
        private LoggerInterface $logger,
        private CommandLogRepositoryInterface $commandLogRepo,
        private CommandLogger $commandLogger
    ) {
    }

    public function setDecoratedHandler(?HandlerInterface $decorated): void
    {
        $this->decorated = $decorated;
    }

    public function getDecoratedHandlerOrThrow(): HandlerInterface
    {
        if (null === $this->decorated) {
            throw new \RuntimeException('The decorated handler has not to be setted');
        }

        return $this->decorated;
    }

    public function acceptCommand(CommandInterface $command): bool
    {
        return $this->getDecoratedHandlerOrThrow()->acceptCommand($command);
    }

    public function handle(CommandInterface $command): void
    {
        $decorated = $this->getDecoratedHandlerOrThrow();
        if (false === $this->commandLogger->mustLog($command)) {
            $decorated->handle($command);
        } else {
            $commandLog = $this->logCommand($command, CommandLogInterface::TYPE_BEFORE_HANDLER);
            try {
                $decorated->handle($command);
                $this->logCommand($command, CommandLogInterface::TYPE_AFTER_HANDLER, $commandLog);
            } catch (\Throwable $e) {
                $this->logCommand($command, CommandLogInterface::TYPE_EXCEPTION, $commandLog, $e);
                throw ($e instanceof \Exception ? $e : new \RuntimeException($e->getMessage(), (int) $e->getCode(), $e));
            }
        }
    }

    /** @param CommandLogInterface::TYPE_* $type */
    private function logCommand(CommandInterface $command, int $type, CommandLogInterface $previousCommandLog = null, \Throwable $exception = null): CommandLogInterface
    {
        $entity = $this->commandLogRepo->createEntity($command, $type, $previousCommandLog, $exception);

        $message = $entity->getMessage();
        $message = null === $message ? '' : $message;

        if (null !== $exception) {
            $this->logger->error($message, ['command' => $entity->getCommandData(), 'type' => $type, 'exception' => $exception]);
        } else {
            $this->logger->info($message, ['command' => $entity->getCommandData(), 'type' => $type]);
        }

        $this->manager->persist($entity);
        $this->manager->flush();

        return $entity;
    }
}
