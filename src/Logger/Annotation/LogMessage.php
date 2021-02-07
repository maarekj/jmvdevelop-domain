<?php

namespace JmvDevelop\Domain\Logger\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class LogMessage
{
    public string $expression = "";
}
