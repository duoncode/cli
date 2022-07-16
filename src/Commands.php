<?php

declare(strict_types=1);

namespace Conia\Cli;

class Commands
{
    protected array $commands = [];

    public function __construct(Command|array $commands = [])
    {
        $this->add($commands);
    }

    protected function addCommand(Command $command): void
    {
        $this->commands[] = $command;
    }

    public function add(Command|array $commands): void
    {
        if (is_array($commands)) {
            foreach ($commands as $command) {
                $this->add($command);
            }
        } else {
            $this->addCommand($commands);
        }
    }

    public function get(): array
    {
        return $this->commands;
    }
}
