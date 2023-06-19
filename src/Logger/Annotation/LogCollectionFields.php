<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Logger\Annotation;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class LogCollectionFields
{
    /** @var string[] */
    public array $fields = [];
}
