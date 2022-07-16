<?php

declare(strict_types=1);

namespace Conia\Cli;

use ErrorException;

class Runner
{
    // The commands ordered by group and name
    protected array $toc;
    // The commands indexed by name only
    protected array $list;
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
            $name = $command->name();
            $desc = $command->description();
            $groups[$command->group() ?: 'General'][$name] = [
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
            $this->output->echo("\n$group\n");

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

    public function run(): string|int
    {
        try {
            if (isset($_SERVER['argv'][1])) {
                $cmd = $_SERVER['argv'][1];

                if ($cmd === 'help') {
                    $this->showHelp();

                    return 0;
                } else {
                    if (isset($this->list[$cmd])) {
                        if (count($this->list[$cmd]) === 1) {
                            return $this->runCommand($this->list[$cmd][0]);
                        }

                        echo "Ambiguous command. Please add the group name:\n";
                        asort($this->list[$cmd]);

                        foreach ($this->list[$cmd] as $command) {
                            $group = strtolower($command->group());
                            $name = strtolower($command->name());
                            echo "    $group:$name\n";
                        }

                        return 1;
                    }

                    echo "\nCommand not found.\n";

                    return 1;
                }
            } else {
                $this->showHelp();

                return 0;
            }
        } catch (ErrorException $e) {
            throw $e;
            echo "\nError while running command '";
            echo (string)($_SERVER['argv'][1] ?? '<no command given>');
            echo "'.\n\n    Error message: " . $e->getMessage() . "\n";

            return 1;
        }
    }
}
