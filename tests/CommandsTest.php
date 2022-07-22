<?php

declare(strict_types=1);

use Conia\Cli\Commands;
use Conia\Cli\Tests\Fixtures\BarStuff;
use Conia\Cli\Tests\Fixtures\Erring;
use Conia\Cli\Tests\Fixtures\FooDrivel;
use Conia\Cli\Tests\Fixtures\FooStuff;

test('Init empty then add one Command', function () {
    $commands = new Commands();
    $foo = new FooStuff();
    $commands->add($foo);

    expect($commands->get()[0])->toBe($foo);
});


test('Init with one Command', function () {
    $foo = new FooStuff();
    $commands = new Commands($foo);

    expect($commands->get()[0])->toBe($foo);
});


test('Init with one Command then add another Command', function () {
    $foo = new FooStuff();
    $commands = new Commands($foo);
    $bar = new BarStuff();
    $commands->add($bar);

    expect($commands->get()[0])->toBe($foo);
    expect($commands->get()[1])->toBe($bar);
});


test('Init with array', function () {
    $foo = new FooStuff();
    $bar = new BarStuff();
    $commands = new Commands([$foo, $bar]);

    expect($commands->get()[0])->toBe($foo);
    expect($commands->get()[1])->toBe($bar);
});


test('Init with array then add another Command', function () {
    $foo = new FooStuff();
    $bar = new BarStuff();
    $commands = new Commands([$foo, $bar]);
    $drivel = new FooDrivel();
    $commands->add($drivel);

    expect($commands->get()[0])->toBe($foo);
    expect($commands->get()[1])->toBe($bar);
    expect($commands->get()[2])->toBe($drivel);
});


test('Init with array then add another array', function () {
    $foo = new FooStuff();
    $bar = new BarStuff();
    $commands = new Commands([$foo, $bar]);
    $drivel = new FooDrivel();
    $err = new Erring();
    $commands->add([$drivel, $err]);

    expect($commands->get()[0])->toBe($foo);
    expect($commands->get()[1])->toBe($bar);
    expect($commands->get()[2])->toBe($drivel);
    expect($commands->get()[3])->toBe($err);
});


test('Init with one Command then add Commands', function () {
    $foo = new FooStuff();
    $commands = new Commands($foo);
    $drivel = new FooDrivel();
    $err = new Erring();
    $commands->add(new Commands([$drivel, $err]));

    expect($commands->get()[0])->toBe($foo);
    expect($commands->get()[1])->toBe($drivel);
    expect($commands->get()[2])->toBe($err);
});


test('Init with array then add Commands', function () {
    $foo = new FooStuff();
    $bar = new BarStuff();
    $commands = new Commands([$foo, $bar]);
    $drivel = new FooDrivel();
    $err = new Erring();
    $commands->add(new Commands([$drivel, $err]));

    expect($commands->get()[0])->toBe($foo);
    expect($commands->get()[1])->toBe($bar);
    expect($commands->get()[2])->toBe($drivel);
    expect($commands->get()[3])->toBe($err);
});
