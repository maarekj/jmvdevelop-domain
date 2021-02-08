<?php

namespace JmvDevelop\Domain;

trait HandlerAwareTrait
{
    protected HandlerInterface $domainHandler;

    /** @required */
    public function setDomainHandler(HandlerInterface $domainHandler): void
    {
        $this->domainHandler = $domainHandler;
    }
}
