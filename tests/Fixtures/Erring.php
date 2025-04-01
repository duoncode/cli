<?php

declare(strict_types=1);

namespace Duon\Cli\Tests\Fixtures;

use Duon\Cli\Command;
use Exception;

class Erring extends Command
{
	protected string $name = 'err';
	protected string $group = 'Errors';
	protected string $prefix = 'err';
	protected string $description = 'Throws an error';

	public function run(): int
	{
		throw new Exception('Red herring');
	}
}
