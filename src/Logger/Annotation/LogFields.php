<?php

namespace JmvDevelop\Domain\Logger\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class LogFields
{
    /** @var string[] */
    public array $fields = [];
}
