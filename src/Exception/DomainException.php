<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Exception;

use Throwable;

if (interface_exists('\\GraphQL\\Error\\ClientAware')) {
    class DomainException extends \RuntimeException implements \GraphQL\Error\ClientAware
    {
        protected bool $clientSafe = true;

        public function __construct(string $message = '', bool $safe = true, int $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
            $this->clientSafe = $safe;
        }

        public function isClientSafe(): bool
        {
            return $this->clientSafe;
        }

        public function setClientSafe(bool $clientSafe): self
        {
            $this->clientSafe = $clientSafe;

            return $this;
        }
    }
} else {
    class DomainException extends \RuntimeException
    {
        protected bool $clientSafe = true;

        public function __construct(string $message = '', bool $safe = true, int $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
            $this->clientSafe = $safe;
        }

        public function isClientSafe(): bool
        {
            return $this->clientSafe;
        }

        public function setClientSafe(bool $clientSafe): self
        {
            $this->clientSafe = $clientSafe;

            return $this;
        }
    }
}
