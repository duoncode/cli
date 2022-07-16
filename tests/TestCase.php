<?php

declare(strict_types=1);

namespace Conia\Cli\Tests;

use Conia\Cli\Commands;
use Conia\Cli\Runner;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function fulltrim(string $text): string
    {
        return trim(
            preg_replace(
                '/> </',
                '><',
                preg_replace(
                    '/\s+/',
                    ' ',
                    preg_replace('/\n/', '', $text)
                )
            )
        );
    }

    public function getCommands(): Commands
    {
        return new Commands([
            new Fixtures\Write(),
        ]);
    }

    public function getRunner(): Runner
    {
        return new Runner($this->getCommands());
    }
}
