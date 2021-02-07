<?php

namespace JmvDevelop\Domain\Logger\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class CommandLogger
{
    public string $service = "";
}
