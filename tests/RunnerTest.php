<?php

declare(strict_types=1);

namespace Duon\Cli\Tests;

class RunnerTest extends TestCase
{
	public function testShowHelpWhenCalledWithoutCommand(): void
	{
		$_SERVER['argv'] = ['run'];
		$runner = $this->getRunner();

		$this->expectOutputRegex("/available commands.*bar.*prints foo's stuff/si");
		$runner->run();
	}

	public function testShowHelpWhenCalledWithHelpCommand(): void
	{
		$_SERVER['argv'] = ['run', 'help'];
		$runner = $this->getRunner();

		$this->expectOutputRegex("/available commands.*prints bar's stuff.*foo/si");
		$runner->run();
	}

	public function testListCommands(): void
	{
		$_SERVER['argv'] = ['run', 'commands'];
		$runner = $this->getRunner();

		$this->expectOutputString("bar:stuff\ndrivel\nerr\nerr:err\nfoo:drivel\nfoo:stuff\n");
		$runner->run();
	}

	public function testShowCommandSpecificHelp(): void
	{
		$_SERVER['argv'] = ['run', 'help', 'foo:stuff'];
		$runner = $this->getRunner();

		$this->expectOutputRegex('/php run foo:stuff.*Options:.*Lorem ipsum/s');
		$runner->run();
	}

	public function testCommandSpecificHelpDefault(): void
	{
		$_SERVER['argv'] = ['run', 'help', 'bar:stuff'];
		$runner = $this->getRunner();

		$this->expectOutputRegex('/php run bar:stuff/');
		$runner->run();
	}

	public function testShowHelpInOrder(): void
	{
		$_SERVER['argv'] = ['run'];
		$runner = $this->getRunner();

		$this->expectOutputRegex('/Available.*Bar.*bar:.*stuff.*Errors.*err:.*err.*Foo.*foo:.*drivel.*stuff/s');
		$runner->run();
	}

	public function testRunSimpleCommand(): void
	{
		$_SERVER['argv'] = ['run', 'drivel'];
		$runner = $this->getRunner();

		$this->expectOutputString("Foo's drivel");
		$runner->run();
	}

	public function testRunAmbiguousCommand(): void
	{
		$_SERVER['argv'] = ['run', 'stuff'];
		$runner = $this->getRunner();

		$this->expectOutputRegex('/Ambiguous.*bar.*:stuff.*foo.*:stuff/s');
		$runner->run();
	}

	public function testRunGroupNameCommand(): void
	{
		$_SERVER['argv'] = ['run', 'bar:stuff'];
		$runner = $this->getRunner();

		$this->expectOutputString("Bar's stuff");
		$runner->run();
	}

	public function testRunUnknownCommand(): void
	{
		$_SERVER['argv'] = ['run', 'unknown'];
		$runner = $this->getRunner();

		$this->expectOutputRegex('/Command not found/');
		$runner->run();
	}

	public function testRunUnknownGroupCommand(): void
	{
		$_SERVER['argv'] = ['run', 'foo:unknown'];
		$runner = $this->getRunner();

		$this->expectOutputRegex('/Command not found/');
		$runner->run();
	}

	public function testRunFailingCommand(): void
	{
		$_SERVER['argv'] = ['run', 'err'];
		$runner = $this->getRunner();

		$this->expectOutputRegex("/Error while.*'err'.*Red herring/s");
		$runner->run();
	}

	public function testRunFailingCommandWithCustomPrefix(): void
	{
		$_SERVER['argv'] = ['run', 'err:err'];
		$runner = $this->getRunner();

		$this->expectOutputRegex("/Error while.*'err:err'.*Red herring/s");
		$runner->run();
	}
}
