<?php

declare(strict_types=1);

namespace FiveOrbs\Cli;

use RuntimeException;

abstract class Command
{
    protected string $name = '';
    protected string $group = '';
    protected string $prefix = '';
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

    public function prefix(): string
    {
        return empty($this->prefix) ? strtolower($this->group) : $this->prefix;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function script(): string
    {
        return $_SERVER['argv'][0] ?? '';
    }

    public function output(Output $output): static
    {
        $this->output = $output;

        return $this;
    }

    public function echo(string $message): void
    {
        if ($this->output) {
            $this->output->echo($message);

            return;
        }

        throw new RuntimeException('Output missing');
    }

    public function color(string $text, string $color, string $background = null): string
    {
        if ($this->output) {
            return $this->output->color($text, $color, $background);
        }

        throw new RuntimeException('Output missing');
    }

    public function indent(
        string $text,
        int $indent,
        ?int $max = null,
    ): string {
        if ($this->output) {
            return $this->output->indent($text, $indent, $max);
        }

        throw new RuntimeException('Output missing');
    }

    public function help(): void
    {
        $this->helpHeader(withOptions: false);
    }

    protected function helpHeader(bool $withOptions = false): void
    {
        $script = $this->script();
        $name = $this->name;
        $prefix = $this->prefix();
        $desc = $this->description;

        if (!empty($desc)) {
            $label = $this->color('Description:', 'brown') . "\n";
            $this->echo("{$label}  {$desc}\n\n");
        }

        $usage = $this->color('Usage:', 'brown') . "\n  php {$script} {$prefix}:{$name}";

        if ($withOptions) {
            $this->echo("{$usage} [options]\n\n");
            $this->echo($this->color('Options:', 'brown') . "\n");
        } else {
            $this->echo("{$usage}\n");
        }
    }

    protected function helpOption(string $option, string $description): void
    {
        $this->echo('    ' . $this->color($option, 'green') . "\n");
        $this->echo($this->indent($description, 8, 80) . "\n");
    }
}
