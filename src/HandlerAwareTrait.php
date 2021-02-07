<?php

namespace JmvDevelop\Domain;

trait HandlerAwareTrait
{
    protected HandlerInterface $domainHandler;

    public function setDomainHandler(HandlerInterface $domainHandler)
    {
        $this->domainHandler = $domainHandler;
    }
}
