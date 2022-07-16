<?php

declare(strict_types=1);

use Conia\Cli\Tests\TestCase;

uses(TestCase::class);

test('Show help when called without command', function () {
    $_SERVER['argv'] = ['run'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex("/available commands.*bar.*prints foo's stuff/si");


test('Show help when called with help command', function () {
    $_SERVER['argv'] = ['run', 'help'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex("/available commands.*prints bar's stuff.*foo/si");


test('Show help in order', function () {
    $_SERVER['argv'] = ['run'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/Available.*Bar.*stuff.*Errors.*err.*Foo.*drivel.*stuff/s');


test('Run simple command', function () {
    $_SERVER['argv'] = ['run', 'drivel'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputString("Foo's drivel");


test('Run ambiguous command', function () {
    $_SERVER['argv'] = ['run', 'stuff'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/Ambiguous.*bar:stuff.*foo:stuff/s');


test('Run group:name command', function () {
    $_SERVER['argv'] = ['run', 'bar:stuff'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputString("Bar's stuff");


test('Run unknown command', function () {
    $_SERVER['argv'] = ['run', 'unknown'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/Command not found/');


test('Run unknown group:command', function () {
    $_SERVER['argv'] = ['run', 'foo:unknown'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/Command not found/');


test('Run failing command', function () {
    $_SERVER['argv'] = ['run', 'err'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex("/Error while.*'err'.*Red herring/s");
