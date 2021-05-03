<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\CommandLoggerInterface;
use JmvDevelop\Domain\Entity\BaseCommandLog;
use JmvDevelop\Domain\Entity\CommandLogInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template T as CommandLogInterface
 * @extends EntityRepository<T>
 */
abstract class BaseCommandLogRepository extends EntityRepository implements CommandLogRepositoryInterface
{
    protected ?string $uniqueId = null;

    public function __construct(
        EntityManagerInterface $manager,
        string $entityClass,
        private CommandLoggerInterface $commandLogger,
        private RequestStack $requestStack,
        private TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($manager, $manager->getClassMetadata($entityClass));
        $this->uniqueId = null;
    }

    /** @param CommandLogInterface::TYPE_* $type */
    public function createEntity(CommandInterface $command, int $type, ?CommandLogInterface $previousCommandLog = null, ?\Throwable $exception = null): CommandLogInterface
    {
        $entity = $this->newInstance();

        $entity->setPreviousCommandLog($previousCommandLog);
        $entity->setType($type);

        $entity->setCommandData($this->commandLogger->log($command));

        $entity->setCommandClass(\get_class($command));
        $entity->setRequest($this->requestStack->getMasterRequest());
        $entity->setCurrentUsername($this->getCurrentUsername());

        if (null !== $exception) {
            $entity->setException($exception);
        }

        if (null === $this->uniqueId) {
            $this->uniqueId = uniqid('request');
        }

        $entity->setRequestId($this->uniqueId);

        return $entity;
    }

    /** @return string[] */
    public function getChoicesForCommandClass(): array
    {
        $qb = $this->createQueryBuilder('command_log');
        $qb->select('command_log.commandClass')->distinct();
        $results = $qb->getQuery()->getScalarResult();

        return array_values(array_map(function (array $row): string {
            /** @psalm-suppress MixedAssignment */
            $value = $row['commandClass'] ?? null;
            if (null === $value || !\is_string($value)) {
                return '';
            } else {
                return $value;
            }
        }, $results));
    }

    /** @return array<string, BaseCommandLog::TYPE_*> */
    public function getChoicesForType(): array
    {
        return BaseCommandLog::getChoicesForType();
    }

    protected function getCurrentUsername(): ?string
    {
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                return $user->getUsername();
            } else {
                return (string) $user;
            }
        }

        return null;
    }

    /** @return T */
    abstract protected function newInstance(): CommandLogInterface;
}
