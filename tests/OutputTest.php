<?php

declare(strict_types=1);

use Duon\Cli\Output;

test('Foreground colors', function () {
	$output = new Output('php://output');

	expect($output->color('test', 'black'))->toBe("\033[0;30mtest\033[0m");
	expect($output->color('test', 'gray'))->toBe("\033[1;30mtest\033[0m");
	expect($output->color('test', 'grey'))->toBe("\033[1;30mtest\033[0m");
	expect($output->color('test', 'red'))->toBe("\033[0;31mtest\033[0m");
	expect($output->color('test', 'lightred'))->toBe("\033[1;31mtest\033[0m");
	expect($output->color('test', 'green'))->toBe("\033[0;32mtest\033[0m");
	expect($output->color('test', 'lightgreen'))->toBe("\033[1;32mtest\033[0m");
	expect($output->color('test', 'brown'))->toBe("\033[0;33mtest\033[0m");
	expect($output->color('test', 'yellow'))->toBe("\033[1;33mtest\033[0m");
	expect($output->color('test', 'blue'))->toBe("\033[0;34mtest\033[0m");
	expect($output->color('test', 'lightblue'))->toBe("\033[1;34mtest\033[0m");
	expect($output->color('test', 'magenta'))->toBe("\033[0;35mtest\033[0m");
	expect($output->color('test', 'lightmagenta'))->toBe("\033[1;35mtest\033[0m");
	expect($output->color('test', 'purple'))->toBe("\033[0;35mtest\033[0m");
	expect($output->color('test', 'lightpurple'))->toBe("\033[1;35mtest\033[0m");
	expect($output->color('test', 'cyan'))->toBe("\033[0;36mtest\033[0m");
	expect($output->color('test', 'lightcyan'))->toBe("\033[1;36mtest\033[0m");
	expect($output->color('test', 'lightgray'))->toBe("\033[0;37mtest\033[0m");
	expect($output->color('test', 'lightgrey'))->toBe("\033[0;37mtest\033[0m");
	expect($output->color('test', 'white'))->toBe("\033[1;37mtest\033[0m");
});

test('Has color support', function () {
	$output = new Output('php://output');
	expect($output->color('test', 'red'))->toBe("\033[0;31mtest\033[0m");
	putenv('NO_COLOR=1');
	expect($output->color('test', 'red'))->toBe('test');
	putenv('NO_COLOR');
});

test('Background colors', function () {
	$output = new Output('php://output');

	expect($output->color('test', 'lightgrey', 'black'))->toBe("\033[0;37;40mtest\033[0m");
	expect($output->color('test', 'white', 'red'))->toBe("\033[1;37;41mtest\033[0m");
	expect($output->color('test', 'lightgreen', 'green'))->toBe("\033[1;32;42mtest\033[0m");
	expect($output->color('test', 'yellow', 'yellow'))->toBe("\033[1;33;43mtest\033[0m");
	expect($output->color('test', 'blue', 'blue'))->toBe("\033[0;34;44mtest\033[0m");
	expect($output->color('test', 'lightpurple', 'purple'))->toBe("\033[1;35;45mtest\033[0m");
	expect($output->color('test', 'purple', 'magenta'))->toBe("\033[0;35;45mtest\033[0m");
	expect($output->color('test', 'cyan', 'cyan'))->toBe("\033[0;36;46mtest\033[0m");
	expect($output->color('test', 'white', 'white'))->toBe("\033[1;37;47mtest\033[0m");
	expect($output->color('test', 'white', 'gray'))->toBe("\033[1;37;47mtest\033[0m");
	expect($output->color('test', 'white', 'grey'))->toBe("\033[1;37;47mtest\033[0m");
});

test('Indent', function () {
	$output = new Output('php://output');
	$lorem = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam ' .
		'nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, ' .
		'sed diam voluptua. At vero eos et accusam et justo duo dolores et ea ' .
		'rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ' .
		'ipsum dolor sit amet.';
	$split = explode("\n", $output->indent($lorem, 4, 40));

	expect($split[0])->toBe('    Lorem ipsum dolor sit amet, consetetur');
	expect($split[4])->toBe('    At vero eos et accusam et justo duo');
});
