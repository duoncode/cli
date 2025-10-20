<?php

declare(strict_types=1);

namespace Duon\Cli;

use BadMethodCallException;
use Throwable;
use ValueError;

/**
 * @api
 */
final class Runner
{
	protected const AMBIGUOUS = 1;
	protected const NOTFOUND = 2;

	// The commands ordered by group and name
	protected array $toc = [];

	// The commands indexed by name only
	protected array $list = [];
	protected Output $output;
	protected int $longestName = 0;

	public function __construct(
		Commands $commands,
		string $output = 'php://output',
	) {
		$this->output = new Output($output);
		$this->orderCommands($commands);
	}

	public function orderCommands(Commands $commands): void
	{
		$groups = [];

		foreach ($commands->get() as $command) {
			$name = strtolower($command->name());
			$prefix = $command->prefix();

			if (array_key_exists($prefix, $groups)) {
				$groups[$prefix]['commands'][$name] = $command;
			} else {
				$group = $command->group() ?: 'General';
				$groups[$prefix] = [
					'title' => empty($prefix) ? 'General' : $group,
					'commands' => [$name => $command],
				];
			}

			$this->list[$name][] = $command;

			$len = strlen($prefix . ':' . $command->name());
			$this->longestName = $len > $this->longestName ? $len : $this->longestName;
		}

		ksort($groups);

		foreach ($groups as $name => $group) {
			$commands = $group['commands'];
			ksort($commands);
			$group['commands'] = $commands;
			$this->toc[$name] = $group;
		}
	}

	public function showHelp(): int
	{
		$script = $_SERVER['argv'][0] ?? '';
		$this->output->echo($this->output->color('Usage:', 'brown') . "\n");
		$this->output->echo("  php {$script} [prefix:]command [arguments]\n\n");
		$this->output->echo("Prefixes are optional if the command is unambiguous.\n\n");
		$this->output->echo("Available commands:\n");
		$this->echoGroup('General');
		$this->echoCommand('', 'commands', 'Lists all available commands');
		$this->echoCommand('', 'help', 'Displays this overview');

		foreach ($this->toc as $group) {
			$this->echoGroup($group['title']);

			foreach ($group['commands'] as $name => $command) {
				$this->echoCommand($command->prefix(), $name, $command->description());
			}
		}

		return 0;
	}

	/**
	 * Displays a list of all available commands.
	 *
	 * With and without namespace/group. If a command appears in more than
	 * one namespace, e. g. foo:cmd and bar:cmd, only the namespaced ones
	 * will be displayed.
	 */
	public function showCommands(): int
	{
		$list = [];

		foreach ($this->toc as $group) {
			foreach ($group['commands'] as $command) {
				$prefix = $command->prefix();

				if ($prefix) {
					$key = "{$prefix}:" . $command->name();
					$list[$key] = ($list[$key] ?? 0) + 1;
				}

				$name = $command->name();
				$list[$name] = ($list[$name] ?? 0) + 1;
			}
		}

		ksort($list);

		foreach ($list as $name => $count) {
			if ($count === 1) {
				$this->output->echo("{$name}\n");
			}
		}

		return 0;
	}

	public function run(): int|string
	{
		try {
			if (isset($_SERVER['argv'][1])) {
				$cmd = strtolower($_SERVER['argv'][1]);
				$isHelpCall = false;

				if ($cmd === 'help') {
					$isHelpCall = true;

					if (isset($_SERVER['argv'][2])) {
						$cmd = strtolower($_SERVER['argv'][2]);
					} else {
						return $this->showHelp();
					}
				}

				if ($cmd === 'commands') {
					return $this->showCommands();
				}

				try {
					return $this->runCommand($this->getCommand($cmd), $isHelpCall);
				} catch (ValueError $e) {
					if ($e->getCode() === self::AMBIGUOUS) {
						return $this->showAmbiguousMessage($cmd);
					}

					throw $e;
				}
			}

			return $this->showHelp();
		} catch (Throwable $e) {
			$this->output->echo("Error while running command '");
			$this->output->echo($_SERVER['argv'][1] ?? '<no command given>');
			$this->output->echo("':\n\n" . $e->getMessage() . "\n");

			return 1;
		}
	}

	protected function echoGroup(string $title): void
	{
		$g = $this->output->color($title, 'brown');
		$this->output->echo("\n{$g}\n");
	}

	protected function echoCommand(string $prefix, string $name, string $desc): void
	{
		$prefix = $prefix ? $prefix . ':' : '';
		$name = $this->output->color($name, 'green');

		// The added magic number takes colorization into
		// account as it lengthens the string.
		$prefixedName = str_pad($prefix . $name, $this->longestName + 13);
		$this->output->echoln("  {$prefixedName}{$desc}");
	}

	protected function showAmbiguousMessage(string $cmd): int
	{
		$this->output->echo("Ambiguous command. Please add the group name:\n\n");
		asort($this->list[$cmd]);

		foreach ($this->list[$cmd] as $command) {
			$prefix = $this->output->color($command->prefix(), 'brown');
			$name = strtolower($command->name());
			$this->output->echoln("  {$prefix}:{$name}");
		}

		return 1;
	}

	protected function getCommand(string $cmd): Command
	{
		if (isset($this->list[$cmd])) {
			if (count($this->list[$cmd]) === 1) {
				return $this->list[$cmd][0];
			}

			throw new ValueError('Ambiguous command', self::AMBIGUOUS);
		} else {
			if (str_contains($cmd, ':')) {
				[$group, $name] = explode(':', $cmd);

				if (isset($this->toc[$group]['commands'][$name])) {
					return $this->toc[$group]['commands'][$name];
				}
			}
		}

		throw new ValueError('Command not found', self::NOTFOUND);
	}

	protected function runCommand(Command $command, bool $isHelpCall): int|string
	{
		if ($isHelpCall) {
			$command->output($this->output)->help();

			return 0;
		}

		return $command->output($this->output)->run();
	}
}
