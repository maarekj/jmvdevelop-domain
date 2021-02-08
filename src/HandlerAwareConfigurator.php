<?php

namespace JmvDevelop\Domain;

final class HandlerAwareConfigurator
{
    public function __construct(private HandlerInterface $handler)
    {
    }

    public function configure(HandlerAwareInterface $handlerAware): void
    {
        $handlerAware->setDomainHandler($this->handler);
    }
}
