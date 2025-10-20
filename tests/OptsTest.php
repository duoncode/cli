<?php

declare(strict_types=1);

namespace Duon\Cli\Tests;

use Duon\Cli\Opts;
use ValueError;

class OptsTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		$_SERVER['argv'] = [
			'run',
			'migrations',
			'--list',
			'1',
			'chuck',
			'-h',
			'--conn',
			'sqlite',
			'--novalues',
		];
	}

	public function testHasOption(): void
	{
		$opts = new Opts();

		$this->assertFalse($opts->has('run'));
		$this->assertFalse($opts->has('migrations'));
		$this->assertTrue($opts->has('-h'));
		$this->assertTrue($opts->has('--conn'));
		$this->assertTrue($opts->has('-c', true));
		$this->assertTrue($opts->has('-c', $opts->has('--conn')));
	}

	public function testGetValue(): void
	{
		$opts = new Opts();

		$this->assertSame('sqlite', $opts->get('--conn'));
		$this->assertSame('sqlite', $opts->get('--conn', 'pgsql'));
		$this->assertSame('default', $opts->get('-h', 'default'));
		$this->assertSame('default', $opts->get('-?', 'default'));
	}

	public function testGetValues(): void
	{
		$opts = new Opts();

		$this->assertSame(['1', 'chuck'], $opts->all('--list'));
		$this->assertSame(['1', 'chuck'], $opts->all('--list', ['2']));
		$this->assertSame(['sqlite'], $opts->all('--conn'));
		$this->assertSame(['1', '2'], $opts->all('--novalues', ['1', '2']));
		$this->assertSame(['1', '2'], $opts->all('--missing', ['1', '2']));
	}

	public function testTryToGetValueFromMissingOption(): void
	{
		$opts = new Opts();

		$this->expectException(ValueError::class);
		$this->expectExceptionMessage('Unknown option: -?');
		$opts->get('-?');
	}

	public function testTryToGetValuesFromMissingOption(): void
	{
		$opts = new Opts();

		$this->expectException(ValueError::class);
		$this->expectExceptionMessage('Unknown option: --missing');
		$opts->all('--missing');
	}

	public function testTryToGetMissingValue(): void
	{
		$opts = new Opts();

		$this->expectException(ValueError::class);
		$this->expectExceptionMessage('No value given for -h');
		$opts->get('-h');
	}

	public function testTryToGetMissingValues(): void
	{
		$opts = new Opts();

		$this->expectException(ValueError::class);
		$this->expectExceptionMessage('No value given for --novalues');
		$opts->all('--novalues');
	}

	public function testGetValueWithEqualsSyntax(): void
	{
		$_SERVER['argv'] = [
			'run',
			'--config=production',
			'--host=localhost:3306',
			'--data=key=value',
		];

		$opts = new Opts();

		$this->assertSame('production', $opts->get('--config'));
		$this->assertSame('localhost:3306', $opts->get('--host'));
		$this->assertSame('key=value', $opts->get('--data'));
	}

	public function testSetMultipleValuesWithEqualsSyntax(): void
	{
		$_SERVER['argv'] = [
			'run',
			'--config=production',
			'--config=staging',
		];

		$opts = new Opts();

		$this->assertSame(['production', 'staging'], $opts->all('--config'));
	}
}
