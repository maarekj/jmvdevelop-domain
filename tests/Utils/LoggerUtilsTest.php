<?php

declare(strict_types=1);

use JmvDevelop\Domain\Logger\ExpressionLanguageProvider;
use JmvDevelop\Domain\Tests\Utils\Fixtures\ObjectWithDoubleNested;
use JmvDevelop\Domain\Tests\Utils\Fixtures\ObjectWithError;
use JmvDevelop\Domain\Tests\Utils\Fixtures\ObjectWithNested;
use JmvDevelop\Domain\Tests\Utils\Fixtures\SimpleObject;
use JmvDevelop\Domain\Utils\LoggerUtils;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

function createLoggerUtils(): LoggerUtils
{
    return new LoggerUtils(
        new ArrayAdapter(),
        new ExpressionLanguageProvider()
    );
}

test('always false', function ($command, $expected): void {
    $logger = createLoggerUtils();

    expect($logger->logCommand($command))->toEqual($expected);
})->with([
    [new SimpleObject('field1', 'field2'), [
        'field2' => 'field2',
        'field1' => 'field1',
    ]],
    [new SimpleObject(null, 'field2'), [
        'field1' => null,
        'field2' => 'field2',
    ]],
    [new ObjectWithNested(new SimpleObject('simple field1', 'simple field2'), 'field1', 'field2'), [
        'field1' => 'field1',
        'field2' => 'field2',
        'simpleObject' => [
            'field1' => 'simple field1',
            'field2' => 'simple field2',
        ],
    ]],
    [new ObjectWithDoubleNested(
        new ObjectWithNested(
            new SimpleObject('simple field1', 'simple field2'),
            'nested field1',
            'nested field2'
        ),
        'field1',
        'field2'
    ), [
        '__command_message__' => 'object_with_double_nested',
        'field1' => 'field1',
        'field2' => 'field2',
        'object' => [
            'field1' => 'nested field1',
            'simpleObject.field1' => 'simple field1',
        ],
    ]],
    [new ObjectWithDoubleNested(
        new ObjectWithNested(
            null,
            'nested field1',
            'nested field2'
        ),
        'field1',
        'field2'
    ), [
        '__command_message__' => 'object_with_double_nested',
        'field1' => 'field1',
        'field2' => 'field2',
        'object' => [
            'field1' => 'nested field1',
            'simpleObject.field1' => null,
        ],
    ]],
    [new ObjectWithDoubleNested(
        null,
        'field1',
        'field2'
    ), [
        '__command_message__' => 'object_with_double_nested',
        'field1' => 'field1',
        'field2' => 'field2',
        'object' => null,
    ]],
    [new ObjectWithError(
        new ObjectWithNested(null, 'nested field1', 'nested field2'),
        'field1',
        'field2'
    ), [
        '__command_message__' => 'object_with_error',
        'field1' => 'field1',
        'field2' => 'field2',
        'object' => [
            'field1' => 'nested field1',
            'erroronpath.field1' => null,
        ],
    ]],
]);
