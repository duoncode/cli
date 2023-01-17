<?php

declare(strict_types=1);

use Conia\Cli\Tests\Fixtures\Erring;
use Conia\Cli\Tests\Fixtures\FooStuff;

test('Command getters', function () {
    $_SERVER['argv'] = ['run'];
    $foo = new FooStuff();

    expect($foo->name())->toBe('stuff');
    expect($foo->description())->toBe("Prints Foo's stuff to stdout");
    expect($foo->script())->toBe('run');

    // Implicit prefix
    expect($foo->group())->toBe('Foo');
    expect($foo->prefix())->toBe('foo');

    // Explicit prefix
    $err = new Erring();
    expect($err->group())->toBe('Errors');
    expect($err->prefix())->toBe('err');
});
