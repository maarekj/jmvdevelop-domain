<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Logger;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\CommandLoggerInterface;
use JmvDevelop\Domain\Logger\Annotation\CommandLogger as CommandLoggerAnnotation;
use JmvDevelop\Domain\Logger\Annotation\NotLog;
use Psr\Container\ContainerInterface;

class CommandLogger implements CommandLoggerInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly CommandLoggerInterface $loggerFallback
    ) {
    }

    public function mustLog(CommandInterface $command): bool
    {
        $refClass = new \ReflectionClass($command);
        $annotations = $refClass->getAttributes(NotLog::class);

        return 0 === \count($annotations);
    }

    /** @return array<string, mixed> */
    public function log(CommandInterface $command): array
    {
        $refClass = new \ReflectionClass($command);

        $annotations = $refClass->getAttributes(CommandLoggerAnnotation::class);
        $annotation = reset($annotations);
        if (false === $annotation) {
            $logger = $this->loggerFallback;
        } else {
            $instance = $annotation->newInstance();
            $logger = $this->container->get($instance->service);
            if (!($logger instanceof CommandLoggerInterface)) {
                throw new \InvalidArgumentException(sprintf('"%s" must be implement %s', $instance->service, CommandLoggerInterface::class));
            }
        }

        return $logger->log($command);
    }
}
