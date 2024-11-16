<?php

declare(strict_types=1);

namespace FiveOrbs\Cli\Tests\Fixtures;

use FiveOrbs\Cli\Command;

class FooDrivel extends Command
{
    protected string $name = 'drivel';
    protected string $group = 'Foo';
    protected string $description = "Prints Foo's drivel to stdout";

    public function run(): int
    {
        $this->echo("Foo's drivel");

        return 0;
    }
}
