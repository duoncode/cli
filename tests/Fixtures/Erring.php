<?php

declare(strict_types=1);

namespace Conia\Cli\Tests\Fixtures;

use Conia\Cli\Command;
use Exception;

class Erring extends Command
{
    protected string $name = 'err';
    protected string $group = 'Errors';
    protected string $description = "Throws an error";

    public function run(): int
    {
        throw new Exception();
    }
}
