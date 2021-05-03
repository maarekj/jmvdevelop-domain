<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__."/src")
    ->in(__DIR__."/tests")
    ->exclude("vendor")
    ->exclude("var")
    ->exclude("bin")
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules(array(
        '@PSR2' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        'declare_strict_types' => true,
        'use_arrow_functions' => false,
        'array_syntax' => array('syntax' => 'short'),
        'native_function_invocation' => true,
        'phpdoc_no_empty_return' => false,
        'phpdoc_to_comment' => false,
        'self_accessor' => false,
    ))
    ->setFinder($finder)
;