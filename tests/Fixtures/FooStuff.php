<?php

declare(strict_types=1);

namespace FiveOrbs\Cli\Tests\Fixtures;

use FiveOrbs\Cli\Command;

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
        $this->helpHeader(withOptions: true);
        $desc = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam ' .
            'nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, ' .
            'sed diam voluptua. At vero eos et accusam et justo duo dolores et ea ' .
            'rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ' .
            'ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing ' .
            'elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna ' .
            'aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo ' .
            'dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus ' .
            'est Lorem ipsum dolor sit amet.';
        $this->helpOption('-s, --stuff <stuff>', $desc);
    }
}
