<?php

declare(strict_types=1);

namespace Conia\Cli\Tests\Fixtures;

use Conia\Cli\Command;

class FooStuff extends Command
{
    protected string $name = 'stuff';
    protected string $group = 'Foo';
    protected string $description = "Prints Foo's stuff to stdout";

    public function run(): int
    {
        $this->echo("Foo's stuff");

        return 0;
    }

    public function help(): void
    {
        $this->echo('foo:stuff help');
    }
}
