<?php

namespace JmvDevelop\Domain;

use JmvDevelop\Domain\Exception\UnhandledException;

class ChainHandler implements HandlerInterface
{
    /** @var HandlerInterface[] */
    private array $handlers;

    /** @param HandlerInterface[] $handlers */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function addHandler(HandlerInterface $handler): self
    {
        $this->handlers[] = $handler;

        return $this;
    }

    public function acceptCommand(CommandInterface $command): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->acceptCommand($command)) {
                return true;
            }
        }

        return false;
    }

    public function handle(CommandInterface $command): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler->acceptCommand($command)) {
                $handler->handle($command);
                return;
            }
        }

        throw new UnhandledException($command);
    }
}
