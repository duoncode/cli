<?php

declare(strict_types=1);

use Duon\Cli\Tests\Fixtures\Erring;
use Duon\Cli\Tests\Fixtures\FooStuff;

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

test('Echo fails', function () {
	$foo = new FooStuff();
	$foo->echo('error');
})->throws(RuntimeException::class, 'Output missing');

test('Echo line fails', function () {
	$foo = new FooStuff();
	$foo->echoln('error');
})->throws(RuntimeException::class, 'Output missing');

test('Color fails', function () {
	$foo = new FooStuff();
	$foo->color('error', '#ffffff');
})->throws(RuntimeException::class, 'Output missing');

test('Indent fails', function () {
	$foo = new FooStuff();
	$foo->indent('error', 1);
})->throws(RuntimeException::class, 'Output missing');
