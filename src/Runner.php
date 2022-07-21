<?php

declare(strict_types=1);

namespace Conia\Cli;

use BadMethodCallException;
use Throwable;
use ValueError;

class Runner
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
        string $output = 'php://output'
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
        $script = $_SERVER['argv'][0];
        $this->output->echo("Usage: php $script [prefix:]command [arguments]\n\n");
        $this->output->echo("Prefixes are optional if the command is unambiguous.\n\n");
        $this->output->echo("Available commands:\n");

        foreach ($this->toc as $group) {
            $g = $this->output->color(ucwords($group['title']), 'yellow');
            $this->output->echo("\n$g\n");

            foreach ($group['commands'] as $name => $command) {
                $desc = $command->description();
                $p = $command->prefix();
                $prefix = $p ? $p . ':' : '';
                $name = $this->output->color($name, 'lightgreen');

                // The added magic number takes colorization into
                // account as it lengthens the string.
                $prefixedName = str_pad($prefix . $name, $this->longestName + 13);
                $this->output->echo("  $prefixedName$desc\n");
            }
        }

        return 0;
    }

    protected function showAmbiguousMessage(string $cmd): int
    {
        $this->output->echo("Ambiguous command. Please add the group name:\n\n");
        asort($this->list[$cmd]);

        foreach ($this->list[$cmd] as $command) {
            $prefix = $this->output->color($command->prefix(), 'yellow');
            $name = strtolower($command->name());
            $this->output->echo("  $prefix:$name\n");
        }

        return 1;
    }

    protected function getCommand(string $cmd): Command
    {
        if (isset($this->list[$cmd])) {
            if (count($this->list[$cmd]) === 1) {
                return  $this->list[$cmd][0];
            } else {
                throw new ValueError('Ambiguous command', self::AMBIGUOUS);
            }
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

                try {
                    return $this->runCommand($this->getCommand($cmd), $isHelpCall);
                } catch (ValueError $e) {
                    if ($e->getCode() === self::AMBIGUOUS) {
                        return $this->showAmbiguousMessage($cmd);
                    }
                } catch (BadMethodCallException) {
                    echo "No help entry for $cmd\n";
                }

                echo "Command not found.\n";

                return 1;
            } else {
                return $this->showHelp();
            }
        } catch (Throwable $e) {
            $this->output->echo("\nError while running command '");
            $this->output->echo((string)($_SERVER['argv'][1] ?? '<no command given>'));
            $this->output->echo("'.\n\n    Error message: " . $e->getMessage() . "\n");

            return 1;
        }
    }
}
