<?php

declare(strict_types=1);

namespace Duon\Cli\Tests\Fixtures;

use Duon\Cli\Command;

class BarStuff extends Command
{
	protected string $name = 'stuff';
	protected string $group = 'Bar';
	protected string $description = "Prints Bar's stuff to stdout";

	public function run(): string
	{
		$this->echo("Bar's stuff");

		return 'done';
	}
}
