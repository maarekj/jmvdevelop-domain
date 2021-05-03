<?php

declare(strict_types=1);

namespace JmvDevelop\Domain;

interface HandlerAwareInterface
{
    public function setDomainHandler(HandlerInterface $domainHandler): void;
}
