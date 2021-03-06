<?php

declare(strict_types=1);

use JmvDevelop\Domain\ChainHandler;
use JmvDevelop\Domain\Exception\UnhandledException;
use JmvDevelop\Domain\Tests\Fixtures\Command1;
use JmvDevelop\Domain\Tests\Fixtures\Command1Handler;
use JmvDevelop\Domain\Tests\Fixtures\Command2;
use JmvDevelop\Domain\Tests\Fixtures\Command2Handler;
use JmvDevelop\Domain\Tests\Fixtures\Command3;

test('accept command (with handlers initialized by constructor)', function (): void {
    $handler = new ChainHandler(new \ArrayObject([
        new Command1Handler(),
        new Command2Handler(),
    ]));

    expect($handler->acceptCommand(new Command1()))->toBeTrue();
    expect($handler->acceptCommand(new Command2()))->toBeTrue();
    expect($handler->acceptCommand(new Command3()))->toBeFalse();
});

test('accept command (with handlers initialized by constructor and by addHandler method)', function (): void {
    $handler = new ChainHandler(new \ArrayObject([
        new Command1Handler(),
    ]));

    $handler->addHandler(new Command2Handler());

    expect($handler->acceptCommand(new Command1()))->toBeTrue();
    expect($handler->acceptCommand(new Command2()))->toBeTrue();
    expect($handler->acceptCommand(new Command3()))->toBeFalse();
});

test('test handle', function (): void {
    $handler = new ChainHandler(new \ArrayObject([new Command1Handler(), new Command2Handler()]));
    $command3 = new Command3();
    $handler->handle($command3);
})->throws(UnhandledException::class);
