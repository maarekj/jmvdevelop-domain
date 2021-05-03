<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Tests\Utils\Fixtures;

use JmvDevelop\Domain\Logger\Annotation\LogFields;

class ObjectWithNested
{
    /** @LogFields(fields={"field1", "field2"}) */
    protected ?SimpleObject $simpleObject;

    protected string $field1;

    protected string $field2;

    public function __construct(?SimpleObject $simpleObject, string $field1, string $field2)
    {
        $this->simpleObject = $simpleObject;
        $this->field1 = $field1;
        $this->field2 = $field2;
    }

    public function getSimpleObject(): ?SimpleObject
    {
        return $this->simpleObject;
    }

    public function getField1(): string
    {
        return $this->field1;
    }

    public function getField2(): string
    {
        return $this->field2;
    }
}
