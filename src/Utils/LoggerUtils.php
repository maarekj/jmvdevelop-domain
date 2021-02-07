<?php

namespace JmvDevelop\Domain\Utils;

use JmvDevelop\Domain\Logger\Annotation\LogCollectionFields;
use JmvDevelop\Domain\Logger\Annotation\LogFields;
use JmvDevelop\Domain\Logger\Annotation\LogMessage;
use JmvDevelop\Domain\Logger\ExpressionLanguageProvider;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class LoggerUtils
{
    private PropertyAccessor $propertyAccessor;
    private ExpressionLanguage $expressionLanguage;

    public function __construct(
        private Reader $annotationReader,
        AdapterInterface $cacheAdapter,
        ExpressionLanguageProvider $languageProvider
    )
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->expressionLanguage = new ExpressionLanguage($cacheAdapter, [$languageProvider]);
    }

    public function logCommand(mixed $command): array
    {
        $class = new \ReflectionClass($command);
        $array = [];

        foreach ($this->getAllProperties($class) as $property) {
            /** @var LogFields|null $logFieldsAnnot */
            $logFieldsAnnot = $this->annotationReader->getPropertyAnnotation($property, LogFields::class);

            /** @var LogCollectionFields|null $logCollectionFieldsAnnot */
            $logCollectionFieldsAnnot = $this->annotationReader->getPropertyAnnotation($property, LogCollectionFields::class);

            if (null !== $logFieldsAnnot) {
                $object = $this->getValue($command, $property);
                if (null === $object) {
                    $array[$property->getName()] = null;
                } else {
                    $array[$property->getName()] = $this->logFields($object, $logFieldsAnnot->fields);
                }
            } elseif (null !== $logCollectionFieldsAnnot) {
                $collection = $this->getValue($command, $property);
                $row = [];
                if (null !== $collection) {
                    foreach ($collection as $object) {
                        $row[] = $this->logFields($object, $logCollectionFieldsAnnot->fields);
                    }
                }
                $array[$property->getName()] = $row;
            } else {
                $array[$property->getName()] = $this->logValue($command, $property);
            }
        }

        $logMessageAnnot = $this->annotationReader->getClassAnnotation($class, LogMessage::class);
        if (null !== $logMessageAnnot) {
            try {
                $array['__command_message__'] = $this->expressionLanguage->evaluate($logMessageAnnot->expression, [
                    'o' => $command,
                ]);
            } catch (\Exception $e) {
            }
        }

        return $array;
    }

    /**
     * @param \ReflectionClass $class
     * @return list<\ReflectionProperty>
     */
    protected function getAllProperties(\ReflectionClass $class): array
    {
        $properties = $class->getProperties();
        while ($class = $class->getParentClass()) {
            $properties = \array_merge([], $properties, $class->getProperties());
        }

        return \array_values(\array_reverse($properties));
    }

    /**
     * @param mixed $object
     * @param string[] $fields
     *
     * @return array<string, scalar>
     */
    protected function logFields($object, array $fields): array
    {
        $array = [];

        foreach ($fields as $field) {
            $array[$field] = $this->logValue($object, $field);
        }

        return $array;
    }

    /**
     * @param mixed $object
     * @param string|\ReflectionProperty $property
     *
     * @return mixed
     */
    protected function logValue($object, $property)
    {
        $value = $this->getValue($object, $property);
        $json = \json_encode($value);

        if ($json === false) {
            return null;
        }

        return $value;
    }

    /**
     * @param mixed $object
     * @param string|\ReflectionProperty $property
     *
     * @return mixed
     */
    protected function getValue($object, $property)
    {
        $property = \is_string($property) ? $property : $property->getName();

        try {
            return $this->propertyAccessor->getValue($object, $property);
        } catch (\Exception $e) {
            return null;
        }
    }
}
