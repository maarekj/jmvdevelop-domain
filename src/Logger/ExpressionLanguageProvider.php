<?php

namespace JmvDevelop\Domain\Logger;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    /** {@inheritdoc} */
    public function getFunctions()
    {
        return [
            new ExpressionFunction('json_encode', function (mixed $arg) {
            }, function (array $variables, mixed $value) {
                return \json_encode($value);
            }),
            new ExpressionFunction('wrap_paren', function (mixed $arg) {
            }, function (array $variables, ?string $value) {
                return null === $value ? '' : '(' . $value . ')';
            }),
        ];
    }
}
