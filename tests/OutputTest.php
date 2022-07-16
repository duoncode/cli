<?php

declare(strict_types=1);

use Conia\Cli\Output;


test('Foreground colors', function () {
    $output = new Output('php://output');

    expect($output->fg('test', 'black'))->toBe("\033[0;30mtest\033[0m");
    expect($output->fg('test', 'darkgray'))->toBe("\033[1;30mtest\033[0m");
    expect($output->fg('test', 'red'))->toBe("\033[0;31mtest\033[0m");
    expect($output->fg('test', 'lightred'))->toBe("\033[1;31mtest\033[0m");
    expect($output->fg('test', 'green'))->toBe("\033[0;32mtest\033[0m");
    expect($output->fg('test', 'lightgreen'))->toBe("\033[1;32mtest\033[0m");
    expect($output->fg('test', 'brown'))->toBe("\033[0;33mtest\033[0m");
    expect($output->fg('test', 'yellow'))->toBe("\033[1;33mtest\033[0m");
    expect($output->fg('test', 'blue'))->toBe("\033[0;34mtest\033[0m");
    expect($output->fg('test', 'lightblue'))->toBe("\033[1;34mtest\033[0m");
    expect($output->fg('test', 'purple'))->toBe("\033[0;35mtest\033[0m");
    expect($output->fg('test', 'lightpurple'))->toBe("\033[1;35mtest\033[0m");
    expect($output->fg('test', 'cyan'))->toBe("\033[0;36mtest\033[0m");
    expect($output->fg('test', 'lightcyan'))->toBe("\033[1;36mtest\033[0m");
    expect($output->fg('test', 'lightgray'))->toBe("\033[0;37mtest\033[0m");
    expect($output->fg('test', 'white'))->toBe("\033[1;37mtest\033[0m");
});
