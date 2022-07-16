<?php

declare(strict_types=1);

namespace Conia\Cli;


abstract class Command
{
    protected string $name = '';
    protected string $section = '';
    protected string $description = '';
    protected Output $output;

    abstract public function run(): string|int;

    public function name(): string
    {
        return $this->name;
    }

    public function section(): string
    {
        return $this->section;
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
        $this->output->echo($message);
    }
}
