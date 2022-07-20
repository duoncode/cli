<?php

declare(strict_types=1);

namespace Conia\Cli;

use BadMethodCallException;

abstract class Command
{
    protected string $name = '';
    protected string $group = '';
    protected string $description = '';
    protected ?Output $output = null;

    abstract public function run(): string|int;

    public function name(): string
    {
        return $this->name;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function output(Output $output): static
    {
        $this->output = $output;

        return $this;
    }

    public function echo(string $message): void
    {
        if (isset($this->output)) {
            $this->output->echo($message);
        }
    }

    public function help(): void
    {
        throw new BadMethodCallException();
    }
}
