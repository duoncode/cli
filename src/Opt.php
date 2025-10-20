<?php

declare(strict_types=1);

namespace Duon\Cli;

class Opt
{
	protected array $values;

	public function __construct(?string $value = null)
	{
		if ($value) {
			$this->values = [$value];
		} else {
			$this->values = [];
		}
	}

	public function set(string $value): void
	{
		$this->values[] = $value;
	}

	public function get(int $index = 0): string
	{
		return $this->values[$index];
	}

	public function all(): array
	{
		return $this->values;
	}

	public function isset(): bool
	{
		return count($this->values) > 0;
	}
}
