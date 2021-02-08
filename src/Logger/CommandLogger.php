<?php

namespace JmvDevelop\Domain\Logger;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\CommandLoggerInterface;
use JmvDevelop\Domain\Logger\Annotation\CommandLogger as CommandLoggerAnnotation;
use JmvDevelop\Domain\Logger\Annotation\NotLog;
use Doctrine\Common\Annotations\Reader;
use Psr\Container\ContainerInterface;

class CommandLogger implements CommandLoggerInterface
{
    public function __construct(
        private ContainerInterface $container,
        private Reader $annotationReader,
        private CommandLoggerInterface $loggerFallback)
    {
    }

    public function mustLog(CommandInterface $command): bool
    {
        $refClass = new \ReflectionClass($command);

        $annotation = $this->annotationReader->getClassAnnotation($refClass, NotLog::class);

        return null === $annotation;
    }

    public function log(CommandInterface $command): array
    {
        $refClass = new \ReflectionClass($command);

        $annotation = $this->annotationReader->getClassAnnotation($refClass, CommandLoggerAnnotation::class);
        if (null == $annotation) {
            $logger = $this->loggerFallback;
        } else {
            /** @psalm-suppress MixedAssignment */
            $logger = $this->container->get($annotation->service);
            if (!($logger instanceof CommandLoggerInterface)) {
                throw new \InvalidArgumentException(\sprintf('"%s" must be implement %s', $annotation->service, CommandLoggerInterface::class));
            }
        }

        return $logger->log($command);
    }
}
