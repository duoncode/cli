<?php

declare(strict_types=1);

namespace FiveOrbs\Cli;

class Commands
{
    protected array $commands = [];

    public function __construct(Command|array $commands = [])
    {
        $this->add($commands);
    }

    public function add(Commands|Command|array $commands): void
    {
        if (is_array($commands)) {
            foreach ($commands as $command) {
                $this->add($command);
            }
        } elseif ($commands instanceof Commands) {
            foreach ($commands->get() as $command) {
                $this->addCommand($command);
            }
        } else {
            $this->addCommand($commands);
        }
    }

    public function get(): array
    {
        return $this->commands;
    }

    protected function addCommand(Command $command): void
    {
        $this->commands[] = $command;
    }
}
