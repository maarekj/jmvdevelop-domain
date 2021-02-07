<?php

namespace JmvDevelop\Domain\Tests\Fixtures;

use JmvDevelop\Domain\CommandInterface;

class BaseCommand implements CommandInterface
{
    private ?int $id;
    private bool $returnValue = false;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getReturnValue(): bool
    {
        return $this->returnValue;
    }

    public function setReturnValue(bool $returnValue): self
    {
        $this->returnValue = $returnValue;

        return $this;
    }
}
