<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Utils;

use JmvDevelop\Domain\Logger\Annotation\LogCollectionFields;
use JmvDevelop\Domain\Logger\Annotation\LogFields;
use JmvDevelop\Domain\Logger\Annotation\LogMessage;
use JmvDevelop\Domain\Logger\ExpressionLanguageProvider;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class LoggerUtils
{
    private readonly PropertyAccessor $propertyAccessor;
    private readonly ExpressionLanguage $expressionLanguage;

    public function __construct(
        CacheItemPoolInterface $cacheAdapter,
        ExpressionLanguageProvider $languageProvider
    ) {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->expressionLanguage = new ExpressionLanguage($cacheAdapter, [$languageProvider]);
    }

    /**
     * @return array<string, mixed>
     */
    public function logCommand(object $command): array
    {
        $class = new \ReflectionClass($command);
        $array = [];

        foreach ($this->getAllProperties($class) as $property) {
            $logFieldsAnnot = $this->getFirstAttribute($property, LogFields::class)?->newInstance();
            $logCollectionFieldsAnnot = $this->getFirstAttribute($property, LogCollectionFields::class)?->newInstance();

            if (null !== $logFieldsAnnot) {
                $object = $this->getValue($command, $property);
                if (null === $object) {
                    $array[$property->getName()] = null;
                } elseif (\is_object($object)) {
                    $array[$property->getName()] = $this->logFields($object, $logFieldsAnnot->fields);
                }
            } elseif (null !== $logCollectionFieldsAnnot) {
                $collection = $this->getValue($command, $property);
                $row = [];
                if (null !== $collection && ($collection instanceof \Traversable || \is_array($collection))) {
                    foreach ($collection as $object) {
                        if (\is_array($object) || \is_object($object)) {
                            $row[] = $this->logFields($object, $logCollectionFieldsAnnot->fields);
                        }
                    }
                }
                $array[$property->getName()] = $row;
            } else {
                $array[$property->getName()] = $this->logValue($command, $property);
            }
        }

        $logMessageAnnot = $this->getFirstAttribute($class, LogMessage::class)?->newInstance();
        if (null !== $logMessageAnnot) {
            try {
                $commandMessage = $this->expressionLanguage->evaluate($logMessageAnnot->expression, [
                    'o' => $command,
                ]);
                $array['__command_message__'] = \is_string($commandMessage) ? $commandMessage : (is_numeric($commandMessage) ? (string) $commandMessage : '');
            } catch (\Exception $e) {
            }
        }

        return $array;
    }

    /**
     * @param \ReflectionClass<object> $class
     *
     * @return list<\ReflectionProperty>
     */
    protected function getAllProperties(\ReflectionClass $class): array
    {
        $properties = $class->getProperties();
        while ($class = $class->getParentClass()) {
            $properties = array_merge([], $properties, $class->getProperties());
        }

        /** @psalm-suppress RedundantCast */
        return array_values(array_reverse($properties));
    }

    /**
     * @param mixed[]|object $object
     * @param string[]       $fields
     *
     * @return mixed[]
     */
    protected function logFields(array|object $object, array $fields): array
    {
        $array = [];

        foreach ($fields as $field) {
            /** @psalm-suppress MixedAssignment */
            $array[$field] = $this->logValue($object, $field);
        }

        return $array;
    }

    /** @param mixed[]|object $object */
    protected function logValue(array|object $object, string|\ReflectionProperty $property): mixed
    {
        /** @psalm-suppress MixedAssignment */
        $value = $this->getValue($object, $property);
        $json = json_encode($value);

        if (false === $json) {
            return null;
        }

        return $value;
    }

    /** @param mixed[]|object $object */
    protected function getValue(array|object $object, string|\ReflectionProperty $property): mixed
    {
        $property = \is_string($property) ? $property : $property->getName();

        try {
            return $this->propertyAccessor->getValue($object, $property);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @template T of object
     *
     * @param \ReflectionProperty|\ReflectionClass<object> $refl
     * @param class-string<T>                              $name
     *
     * @return \ReflectionAttribute<T>|null
     */
    private function getFirstAttribute(\ReflectionProperty|\ReflectionClass $refl, string $name): null|object
    {
        $attributes = $refl->getAttributes($name);
        $attr = reset($attributes);

        return false === $attr ? null : $attr;
    }
}
