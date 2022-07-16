<?php

declare(strict_types=1);

namespace Conia\Cli;

class Output
{
    protected mixed $stream;

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
}
