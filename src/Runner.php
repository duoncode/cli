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
            $desc = $command->description();
            $groups[strtolower($command->group()) ?: 'General'][$name] = [
                'command' => $command,
                'description' => $desc,
            ];

            $this->list[$name][] = $command;

            $len = strlen($command->name());
            $this->longestName = $len > $this->longestName ? $len : $this->longestName;
        }

        ksort($groups);

        foreach ($groups as $group => $commands) {
            ksort($commands);
            $this->toc[$group] = $commands;
        }
    }

    public function showHelp(): int
    {
        $this->output->echo("Available commands:\n");

        foreach ($this->toc as $group => $subCommands) {
            $g = $this->output->color(ucwords($group), 'yellow');
            $this->output->echo("\n$g\n");

            foreach ($subCommands as $name => $command) {
                $desc = $command['description'];
                $name = $this->output->color($name, 'lightgreen');
                $this->output->echo("  $name $desc\n");
            }
        }

        return 0;
    }

    protected function showAmbiguousMessage(string $cmd): int
    {
        $this->output->echo("Ambiguous command. Please add the group name:\n\n");
        asort($this->list[$cmd]);

        foreach ($this->list[$cmd] as $command) {
            $group = $this->output->color(strtolower($command->group()), 'yellow');
            $name = strtolower($command->name());
            $this->output->echo("  $group:$name\n");
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

                if (isset($this->toc[$group][$name])) {
                    return $this->toc[$group][$name]['command'];
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
