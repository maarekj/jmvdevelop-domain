<?php

namespace JmvDevelop\Domain\Utils;

use JmvDevelop\Domain\Exception\ValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatorUtils
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @param mixed $command
     * @param Constraint|Constraint[] $constraints
     * @param string|GroupSequence|(string|GroupSequence)[]|null $group
     */
    public function validateOrThrow(mixed $command, $constraints = null, $group = null): void
    {
        $violations = $this->validator->validate($command, $constraints, $group);

        if (\count($violations) > 0) {
            throw new ValidationException($violations);
        }
    }
}
