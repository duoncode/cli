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


test('List commands (autocomplete)', function () {
    $_SERVER['argv'] = ['run', 'commands'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputString("bar:stuff\ndrivel\nerr\nerr:err\nfoo:drivel\nfoo:stuff\n");


test('Show command specific help', function () {
    $_SERVER['argv'] = ['run', 'help', 'foo:stuff'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/php run foo:stuff.*Options:.*Lorem ipsum/s');


test('Command specific help default', function () {
    $_SERVER['argv'] = ['run', 'help', 'bar:stuff'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/php run bar:stuff/');


test('Show help in order', function () {
    $_SERVER['argv'] = ['run'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/Available.*Bar.*bar:.*stuff.*Errors.*err:.*err.*Foo.*foo:.*drivel.*stuff/s');


test('Run simple command', function () {
    $_SERVER['argv'] = ['run', 'drivel'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputString("Foo's drivel");


test('Run ambiguous command', function () {
    $_SERVER['argv'] = ['run', 'stuff'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex("/Ambiguous.*bar.*:stuff.*foo.*:stuff/s");


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


test('Run failing command with custom prefix', function () {
    $_SERVER['argv'] = ['run', 'err:err'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex("/Error while.*'err:err'.*Red herring/s");
