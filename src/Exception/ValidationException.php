<?php

namespace JmvDevelop\Domain\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends DomainException
{
    private ConstraintViolationListInterface $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $message = \implode("\n", \array_map(function (ConstraintViolationInterface $violation) {
            return \sprintf('%s: %s', $violation->getPropertyPath(), (string)$violation->getMessage());
        }, \iterator_to_array($violations)));
        parent::__construct($message);

        $this->violations = $violations;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
