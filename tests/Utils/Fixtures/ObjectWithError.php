<?php

namespace JmvDevelop\Domain\Tests\Utils\Fixtures;

use JmvDevelop\Domain\Logger\Annotation\LogMessage;
use JmvDevelop\Domain\Logger\Annotation\LogFields;

/**
 * @LogMessage(expression="'object_with_error'")
 */
class ObjectWithError
{
    /** @LogFields(fields={"field1", "erroronpath.field1"}) */
    protected ?ObjectWithNested $object;

    protected string $field1;

    protected string $field2;

    public function __construct(?ObjectWithNested $object, string $field1, string $field2)
    {
        $this->object = $object;
        $this->field1 = $field1;
        $this->field2 = $field2;
    }

    public function getObject(): ?ObjectWithNested
    {
        return $this->object;
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
