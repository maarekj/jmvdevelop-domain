<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Exception;

use Throwable;

if (interface_exists('\\GraphQL\\Error\\ClientAware')) {
    class DomainException extends \RuntimeException implements \GraphQL\Error\ClientAware
    {
        protected bool $clientSafe = true;
        protected string $category = 'domain';

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

        public function getCategory(): string
        {
            return $this->category;
        }

        public function setCategory(string $category): self
        {
            $this->category = $category;

            return $this;
        }
    }
} else {
    class DomainException extends \RuntimeException
    {
        protected bool $clientSafe = true;
        protected string $category = 'domain';

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

        public function getCategory(): string
        {
            return $this->category;
        }

        public function setCategory(string $category): self
        {
            $this->category = $category;

            return $this;
        }
    }
}
