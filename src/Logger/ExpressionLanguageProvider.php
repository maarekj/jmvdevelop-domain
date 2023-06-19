<?php

declare(strict_types=1);

namespace JmvDevelop\Domain\Logger;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('json_encode', function (mixed $arg): void {
            }, function (array $variables, mixed $value) {
                return json_encode($value);
            }),
            new ExpressionFunction('wrap_paren', function (mixed $arg): void {
            }, function (array $variables, ?string $value) {
                return null === $value ? '' : '('.$value.')';
            }),
        ];
    }
}
