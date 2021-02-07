<?php

namespace JmvDevelop\Domain\Logger\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class LogCollectionFields
{
    /** @var string[] */
    public array $fields = [];
}
