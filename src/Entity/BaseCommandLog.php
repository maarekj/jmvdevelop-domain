<?php

namespace JmvDevelop\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;

/**
 * BaseCommandLog.
 *
 * @ORM\MappedSuperclass()
 */
abstract class BaseCommandLog implements CommandLogInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /** @ORM\OneToOne(targetEntity=CommandLogInterface::class) */
    protected ?CommandLogInterface $previousCommandLog = null;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected ?string $sessionId = null;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected ?string $requestId = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var ?CommandLogInterface::TYPE_*
     */
    protected ?int $type = null;

    /** @ORM\Column(type="string", length=1000, nullable=true) */
    protected ?string $message = null;

    /** @ORM\Column(type="json", nullable=true) */
    protected ?array $commandData = null;

    /** @ORM\Column(type="string", nullable=false) */
    protected ?string $commandClass = null;

    /** @ORM\Column(type="json", nullable=true) */
    protected ?array $request = null;

    /** @ORM\Column(type="string", length=50, nullable=true) */
    protected ?string $clientIp = null;

    /** @ORM\Column(type="string", length=400, nullable=true) */
    protected ?string $pathInfo = null;

    /** @ORM\Column(type="string", length=400, nullable=true) */
    protected ?string $uri = null;

    /** @ORM\Column(type="string", length=180, nullable=true) */
    protected ?string $currentUsername = null;

    /** @ORM\Column(type="text", nullable=true) */
    protected ?string $exceptionMessage = null;

    /** @ORM\Column(type="text", nullable=true) */
    protected ?string $exceptionFullMessage = null;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected ?string $exceptionClass = null;

    /** @ORM\Column(type="json", nullable=true) */
    protected ?array $exceptionData = null;

    /** @ORM\Column(type="datetimetz_immutable", nullable=false) */
    protected \DateTimeImmutable $date;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }

    //------------------------------------------------------------------------
    // region Static
    //------------------------------------------------------------------------

    public static function getLabelForType(int $type): string
    {
        switch ($type) {
            case self::TYPE_EXCEPTION:
                return 'exception';
            case self::TYPE_BEFORE_HANDLER:
                return 'before_handler';
            case self::TYPE_AFTER_HANDLER:
                return 'after_handler';
            default:
                throw new \InvalidArgumentException();
        }
    }

    /** @return array<string, BaseCommandLog::TYPE_*> */
    public static function getChoicesForType(): array
    {
        return [
            self::getLabelForType(self::TYPE_EXCEPTION) => self::TYPE_EXCEPTION,
            self::getLabelForType(self::TYPE_BEFORE_HANDLER) => self::TYPE_BEFORE_HANDLER,
            self::getLabelForType(self::TYPE_AFTER_HANDLER) => self::TYPE_AFTER_HANDLER,
        ];
    }

    // endregion

    //------------------------------------------------------------------------
    // region Getters & Setters
    //------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getPreviousCommandLog(): ?CommandLogInterface
    {
        return $this->previousCommandLog;
    }

    public function setPreviousCommandLog(?CommandLogInterface $previousCommandLog = null): void
    {
        $this->previousCommandLog = $previousCommandLog;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /** @return ?CommandLogInterface::TYPE_* */
    public function getType(): ?int
    {
        return $this->type;
    }

    /** @param ?CommandLogInterface::TYPE_* $type */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $message = \substr(null !== $message ? $message : '', 0, 1000);
        $this->message = $message;
    }

    public function getCommandData(): ?array
    {
        return $this->commandData;
    }

    public function setCommandData(?array $commandData): void
    {
        $this->commandData = $commandData;

        /** @psalm-suppress MixedAssignment */
        $message = isset($this->commandData['__command_message__']) ? $this->commandData['__command_message__'] : null;
        $this->setMessage($message === null ? null : (string)$message);
    }

    public function getCommandClass(): ?string
    {
        return $this->commandClass;
    }

    public function setCommandClass(?string $commandClass): void
    {
        $this->commandClass = $commandClass;
    }

    public function getCurrentUsername(): ?string
    {
        return $this->currentUsername;
    }

    public function setCurrentUsername(?string $currentUsername): void
    {
        $this->currentUsername = $currentUsername;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getRequest(): ?array
    {
        return $this->request;
    }

    public function setRequest(?Request $request = null): void
    {
        if (null === $request) {
            $this->request = null;
            $this->pathInfo = null;
            $this->uri = null;
            $this->clientIp = null;
            $this->sessionId = null;
        } else {
            $this->request = [
                'pathInfo' => $request->getPathInfo(),
                'uri' => $request->getUri(),
                'clientIp' => $request->getClientIp(),
                'clientIps' => $request->getClientIps(),
                'basePath' => $request->getBasePath(),
                'host' => $request->getHost(),
                'languages' => $request->getLanguages(),
                'charsets' => $request->getCharsets(),
                'schemeAndHttpHost' => $request->getSchemeAndHttpHost(),
                'requestUri' => $request->getRequestUri(),
                'realMethod' => $request->getRealMethod(),
                'queryString' => $request->getQueryString(),
                'port' => $request->getPort(),
                'method' => $request->getMethod(),
                'locale' => $request->getLocale(),
                'baseUrl' => $request->getBaseUrl(),
                'query' => $request->query->all(),
                'request' => $request->request->all(),
                'server' => $request->server->all(),
                'files' => $request->files->all(),
            ];
            $this->pathInfo = $request->getPathInfo();
            $this->uri = $request->getUri();
            $this->clientIp = $request->getClientIp();
            if ($request->hasSession()) {
                $this->sessionId = $request->getSession()->getId();
            }
        }
    }

    public function setException(\Throwable $exception = null): void
    {
        if (null === $exception) {
            $this->exceptionMessage = null;
            $this->exceptionFullMessage = null;
            $this->exceptionClass = null;
            $this->exceptionData = null;
        } else {
            $this->exceptionMessage = $exception->getMessage();
            $this->exceptionFullMessage = self::exceptionFullMessage($exception);
            $this->exceptionClass = \get_class($exception);
            $this->exceptionData = self::exceptionToArray($exception);
        }
    }

    protected static function exceptionFullMessage(\Throwable $exception): string
    {
        $message = $exception->getMessage();

        if (null !== $exception->getPrevious()) {
            $message .= ' ' . self::exceptionFullMessage($exception->getPrevious());
        }

        return $message;
    }

    protected static function exceptionToArray(\Throwable $exception): array
    {
        $array = [
            'exception_class' => \get_class($exception),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'traceAsString' => $exception->getTraceAsString(),
        ];

        if (null !== $exception->getPrevious()) {
            $array['previous'] = self::exceptionToArray($exception->getPrevious());
        }

        return $array;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function setRequestId(?string $requestId): void
    {
        $this->requestId = $requestId;
    }

    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    public function setClientIp(?string $clientIp): void
    {
        $this->clientIp = $clientIp;
    }

    public function getPathInfo(): ?string
    {
        return $this->pathInfo;
    }

    public function setPathInfo(?string $pathInfo): void
    {
        $this->pathInfo = $pathInfo;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): void
    {
        $this->uri = $uri;
    }

    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    public function setExceptionMessage(?string $exceptionMessage): void
    {
        $this->exceptionMessage = $exceptionMessage;
    }

    public function getExceptionFullMessage(): ?string
    {
        return $this->exceptionFullMessage;
    }

    public function setExceptionFullMessage(?string $exceptionFullMessage): void
    {
        $this->exceptionFullMessage = $exceptionFullMessage;
    }

    public function getExceptionClass(): ?string
    {
        return $this->exceptionClass;
    }

    public function setExceptionClass(?string $exceptionClass): void
    {
        $this->exceptionClass = $exceptionClass;
    }

    public function getExceptionData(): ?array
    {
        return $this->exceptionData;
    }

    public function setExceptionData(?array $exceptionData): void
    {
        $this->exceptionData = $exceptionData;
    }

    // endregion
}
