<?php

declare(strict_types=1);

namespace Conia\Cli;

use Throwable;

class Runner
{
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

    public function showHelp(): void
    {
        echo "\nAvailable commands:\n";

        foreach ($this->toc as $group => $subCommands) {
            $g = ucwords($group);
            $this->output->echo("\n$g\n");

            foreach ($subCommands as $name => $command) {
                $desc = $command['description'];
                $this->output->echo("    $name $desc\n");
            }
        }
    }

    protected function runCommand(Command $cmd): string|int
    {
        return $cmd->output($this->output)->run();
    }

    protected function showAmbiguousMessage(string $cmd): void
    {
        $this->output->echo("Ambiguous command. Please add the group name:\n");
        asort($this->list[$cmd]);

        foreach ($this->list[$cmd] as $command) {
            $group = strtolower($command->group());
            $name = strtolower($command->name());
            $this->output->echo("    $group:$name\n");
        }
    }

    public function run(): string|int
    {
        try {
            if (isset($_SERVER['argv'][1])) {
                $cmd = strtolower($_SERVER['argv'][1]);

                if ($cmd === 'help') {
                    $this->showHelp();

                    return 0;
                } else {
                    if (isset($this->list[$cmd])) {
                        if (count($this->list[$cmd]) === 1) {
                            return $this->runCommand($this->list[$cmd][0]);
                        }

                        $this->showAmbiguousMessage($cmd);

                        return 1;
                    } else {
                        if (str_contains($cmd, ':')) {
                            [$group, $name] = explode(':', $cmd);

                            if (isset($this->toc[$group][$name])) {
                                return $this->runCommand($this->toc[$group][$name]['command']);
                            }
                        }
                    }

                    echo "\nCommand not found.\n";

                    return 1;
                }
            } else {
                $this->showHelp();

                return 0;
            }
        } catch (Throwable $e) {
            $this->output->echo("\nError while running command '");
            $this->output->echo((string)($_SERVER['argv'][1] ?? '<no command given>'));
            $this->output->echo("'.\n\n    Error message: " . $e->getMessage() . "\n");

            return 1;
        }
    }
}
