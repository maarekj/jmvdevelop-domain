<?php

namespace JmvDevelop\Domain;

interface HandlerAwareInterface
{
    public function setDomainHandler(HandlerInterface $domainHandler): void;
}
