<?php

declare(strict_types=1);

namespace Conia\Cli\Tests\Fixtures;

use Conia\Cli\Command;

class Write extends Command
{
    protected string $name = 'print';
    protected string $section = 'Output';
    protected string $description = 'Prints stuff to stdout';

    public function run(): int
    {
        $this->echo('stuff');

        return 0;
    }
}
