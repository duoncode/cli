<?php

declare(strict_types=1);

namespace Conia\Cli;

class Output
{
    protected mixed $stream;
    protected array $fg = [
        'black' => [0, 30],
        'darkgray' => [1, 30],
        'blue' => [0, 34],
        'lightblue' => [1, 34],
        'green' => [0, 32],
        'lightgreen' => [1, 32],
        'cyan' => [0, 36],
        'lightcyan' => [1, 36],
        'red' => [0, 31],
        'lightred' => [1, 31],
        'purple' => [0, 35],
        'lightpurple' => [1, 35],
        'brown' => [0, 33],
        'yellow' => [1, 33],
        'lightgray' => [0, 37],
        'white' => [1, 37],
    ];

    public function __construct(protected readonly string $target)
    {
    }

    protected function getStream(): mixed
    {
        if (!isset($this->stream)) {
            $this->stream = fopen($this->target, 'w');
        }

        return $this->stream;
    }

    public function echo(string $message): void
    {
        fwrite($this->getStream(), $message);
        fflush($this->stream);
    }

    public function fg(string $text, string $color): string
    {
        [$first, $second] = $this->fg[$color];

        return "\033[$first;${second}m$text\033[0m";
    }
}
