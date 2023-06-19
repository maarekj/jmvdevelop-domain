<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Logger\Annotation;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class LogFields
{
    /**
     * @param list<string> $fields
     */
    public function __construct(public readonly array $fields = [])
    {
    }
}
