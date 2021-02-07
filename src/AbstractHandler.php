<?php

namespace JmvDevelop\Domain;

/**
 * Class AbstractHandler.
 */
abstract class AbstractHandler implements HandlerInterface
{
    /** @var string[] */
    private array $acceptedCommandClasses;

    /**
     * AbstractHandler constructor.
     *
     * @param string[]|string $acceptedCommandClasses
     */
    public function __construct(array|string $acceptedCommandClasses)
    {
        $this->acceptedCommandClasses = (array)$acceptedCommandClasses;
    }

    /**
     * @param CommandInterface $command
     *
     * @return bool Return true if this handler accept the command
     */
    public function acceptCommand(CommandInterface $command): bool
    {
        foreach ($this->acceptedCommandClasses as $class) {
            if (true === $command instanceof $class) {
                return true;
            }
        }

        return false;
    }

    public function handle(CommandInterface $command): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @psalm-suppress UndefinedMethod */
        $this->handleCommand($command);
    }
}
