<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Tests\Utils\Fixtures;

use JmvDevelop\Domain\Logger\Annotation\LogMessage;

#[LogMessage(expression: 'error.onExpression(r) ~ "error"')]
class SimpleObject
{
    protected ?string $field1;
    protected string $field2;

    public function __construct(?string $field1, string $field2)
    {
        $this->field1 = $field1;
        $this->field2 = $field2;
    }

    public function getField1(): ?string
    {
        return $this->field1;
    }

    public function getField2(): string
    {
        return $this->field2;
    }
}
