<?php

require_once __DIR__.'/../../autoload.php';
require_once __DIR__.'/../Cache/RemoveCache.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
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

		// create some instances
		$finder = new Finder();
		$fs = new Filesystem();

		// build path to the rootdirectory
		$rootPath = __DIR__ . '/../..';

		// check if the installers cache is empty
		$finder->files()->in($rootPath . '/install/cache');
		$finder->files()->notName('installed.txt');
		$this->assertCount(0, $finder);

		// check if the frontends cache is empty
		$finder->files()->in($rootPath . '/frontend/cache');
		$this->assertCount(0, $finder);

		// check if the frontends cache is empty
		$finder->files()->in($rootPath . '/backend/cache');
		$this->assertCount(0, $finder);
	}
}