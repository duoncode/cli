<?php

declare(strict_types=1);

use Conia\Cli\Tests\TestCase;

uses(TestCase::class);


test('Show help when called without command', function () {
    $_SERVER['argv'] = ['run'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/available commands.*output.*prints stuff/si');


test('Show help when called with help command', function () {
    $_SERVER['argv'] = ['run', 'help'];
    $runner = $this->getRunner();
    $runner->run();
})->expectOutputRegex('/available commands.*output.*prints stuff/si');
