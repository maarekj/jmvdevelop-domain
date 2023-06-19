<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Logger\Annotation;

#[\Attribute(\Attribute::TARGET_CLASS)]
class CommandLogger
{
    public string $service = '';
}
