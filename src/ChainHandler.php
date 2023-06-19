<?php

declare(strict_types=1);

namespace JmvDevelop\Domain;

use JmvDevelop\Domain\Exception\UnhandledException;

class ChainHandler implements HandlerInterface
{
    /** @var list<HandlerInterface> */
    private array $handlers;

    /** @param \Traversable<array-key, HandlerInterface> $handlers */
    public function __construct(\Traversable $handlers)
    {
        $this->handlers = array_values(iterator_to_array($handlers));
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
