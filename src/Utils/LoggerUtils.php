<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Utils;

use Doctrine\Common\Annotations\Reader;
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
    private PropertyAccessor $propertyAccessor;
    private ExpressionLanguage $expressionLanguage;

    public function __construct(
        private Reader $annotationReader,
        CacheItemPoolInterface $cacheAdapter,
        ExpressionLanguageProvider $languageProvider
    ) {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->expressionLanguage = new ExpressionLanguage($cacheAdapter, [$languageProvider]);
    }

    public function logCommand(object $command): array
    {
        $class = new \ReflectionClass($command);
        $array = [];

        foreach ($this->getAllProperties($class) as $property) {
            $logFieldsAnnot = $this->annotationReader->getPropertyAnnotation($property, LogFields::class);
            $logCollectionFieldsAnnot = $this->annotationReader->getPropertyAnnotation($property, LogCollectionFields::class);

            if (null !== $logFieldsAnnot) {
                /** @psalm-suppress MixedAssignment */
                $object = $this->getValue($command, $property);
                if (null === $object) {
                    $array[$property->getName()] = null;
                } elseif (\is_object($object)) {
                    $array[$property->getName()] = $this->logFields($object, $logFieldsAnnot->fields);
                }
            } elseif (null !== $logCollectionFieldsAnnot) {
                /** @psalm-suppress MixedAssignment */
                $collection = $this->getValue($command, $property);
                $row = [];
                if (null !== $collection && ($collection instanceof \Traversable || \is_array($collection))) {
                    /** @psalm-suppress MixedAssignment */
                    foreach ($collection as $object) {
                        if (\is_array($object) || \is_object($object)) {
                            $row[] = $this->logFields($object, $logCollectionFieldsAnnot->fields);
                        }
                    }
                }
                $array[$property->getName()] = $row;
            } else {
                /** @psalm-suppress MixedAssignment */
                $array[$property->getName()] = $this->logValue($command, $property);
            }
        }

        $logMessageAnnot = $this->annotationReader->getClassAnnotation($class, LogMessage::class);
        if (null !== $logMessageAnnot) {
            try {
                $array['__command_message__'] = (string) $this->expressionLanguage->evaluate($logMessageAnnot->expression, [
                    'o' => $command,
                ]);
            } catch (\Exception $e) {
            }
        }

        return $array;
    }

    /**
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
     * @param string[] $fields
     */
    protected function logFields(array | object $object, array $fields): array
    {
        $array = [];

        foreach ($fields as $field) {
            /** @psalm-suppress MixedAssignment */
            $array[$field] = $this->logValue($object, $field);
        }

        return $array;
    }

    protected function logValue(array | object $object, string | \ReflectionProperty $property): mixed
    {
        /** @psalm-suppress MixedAssignment */
        $value = $this->getValue($object, $property);
        $json = json_encode($value);

        if (false === $json) {
            return null;
        }

        return $value;
    }

    protected function getValue(array | object $object, string | \ReflectionProperty $property): mixed
    {
        $property = \is_string($property) ? $property : $property->getName();

        try {
            return $this->propertyAccessor->getValue($object, $property);
        } catch (\Exception $e) {
            return null;
        }
    }
}
