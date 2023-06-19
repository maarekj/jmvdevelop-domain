<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Entity;

use Symfony\Component\HttpFoundation\Request;

interface CommandLogInterface
{
    public const TYPE_BEFORE_HANDLER = 0;
    public const TYPE_AFTER_HANDLER = 1;
    public const TYPE_EXCEPTION = 2;

    public static function getLabelForType(int $type): string;

    /** @return array<string, BaseCommandLog::TYPE_*> */
    public static function getChoicesForType(): array;

    public function getId(): ?int;

    public function setId(?int $id): void;

    public function getPreviousCommandLog(): ?self;

    public function setPreviousCommandLog(self $previousCommandLog = null): void;

    public function getSessionId(): ?string;

    /** @return ?self::TYPE_* */
    public function getType(): ?int;

    /**
     * @param ?self::TYPE_* $type
     */
    public function setType(?int $type): void;

    public function getMessage(): ?string;

    public function setMessage(?string $message): void;

    /** @return array<string, mixed>|null */
    public function getCommandData(): ?array;

    /** @param array<string, mixed>|null $commandData */
    public function setCommandData(?array $commandData): void;

    public function getCommandClass(): ?string;

    public function setCommandClass(?string $commandClass): void;

    public function getCurrentUsername(): ?string;

    public function setCurrentUsername(?string $currentUsername): void;

    public function getDate(): \DateTimeImmutable;

    /** @return array<string, mixed>|null */
    public function getRequest(): ?array;

    public function setRequest(Request $request = null): void;

    public function setException(\Throwable $exception = null): void;

    public function getRequestId(): ?string;

    public function setRequestId(?string $requestId): void;

    public function getClientIp(): ?string;

    public function setClientIp(?string $clientIp): void;

    public function getPathInfo(): ?string;

    public function setPathInfo(?string $pathInfo): void;

    public function getUri(): ?string;

    public function setUri(?string $uri): void;

    public function getExceptionMessage(): ?string;

    public function setExceptionMessage(?string $exceptionMessage): void;

    public function getExceptionFullMessage(): ?string;

    public function setExceptionFullMessage(?string $exceptionFullMessage): void;

    public function getExceptionClass(): ?string;

    public function setExceptionClass(?string $exceptionClass): void;

    /** @return array<string, mixed>|null */
    public function getExceptionData(): ?array;

    /** @param array<string, mixed>|null $exceptionData */
    public function setExceptionData(?array $exceptionData): void;
}
