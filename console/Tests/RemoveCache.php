<?php

require_once __DIR__.'/../../autoload.php';
require_once __DIR__.'/../Cache/RemoveCache.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Fork\ConsoleBundle\Command\RemoveCache;

class RemoveCacheCommandTest extends \PHPUnit_Framework_TestCase
{
	public function testExecute()
	{
		$application = new Application();
		$application->add(new RemoveCache);

		$command = $application->find('cache:remove');
		$commandTester = new CommandTester($command);
		$commandTester->execute(
			array('command' => $command->getName())
		);

		// check the output
		$this->assertRegExp('/All done/', $commandTester->getDisplay());
	}
}