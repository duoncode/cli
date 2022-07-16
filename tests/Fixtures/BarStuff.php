<?php

declare(strict_types=1);

namespace Conia\Cli\Tests\Fixtures;

use Conia\Cli\Command;

class BarStuff extends Command
{
    protected string $name = 'stuff';
    protected string $section = 'Bar';
    protected string $description = "Prints Bar's stuff to stdout";

    public function run(): int
    {
        $this->echo("Bar's stuff");

        return 0;
    }
}
