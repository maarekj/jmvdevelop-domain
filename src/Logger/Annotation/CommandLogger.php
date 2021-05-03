<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Logger\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class CommandLogger
{
    public string $service = '';
}
