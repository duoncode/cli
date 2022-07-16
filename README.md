Conia Cli
=========

A command line interface helper like [Laravel's Artisan](https://laravel.com/docs/9.x/artisan) 
with way less magic.

## Installation

    composer require conia/cli

## Quick Start

Create a Command

```php
use Conia\Cli\Command;

class MyCommand extends Command {
    protected string $name = 'mycommand';
    protected string $group = 'MyGroup';
    protected string $description = 'This is my command description';

    public function run(): int
    {
        $this->echo("Run my command\n");

        return 0;
    }

    // optional
    public function help(): void
    {
        $this->echo('Help entry for mycommand\n");
    }
}
```

Create a runner script, e. g. `run.php`:

```php
<?php

use Conia\Cli\{Runner, Commands};
use MyCommand;

$commands = new Commands([new MyCommand()]);
$runner = new Runner($commands);
$runner-run();
```

Run the command:

```console
$ php run.php mycommand
Run my command

$ php run.php mygroup:mycommand
Run my command

$ php run.php help
Available commands:

MyGroup
    mycommand  This is my command description

$ php run.php help mycommand
Help entry for my command
```
