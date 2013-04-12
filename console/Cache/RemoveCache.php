<?php

namespace Fork\ConsoleBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class RemoveCache extends Command
{
	protected function configure()
	{
		$this
			->setName('cache:remove')
			->setDescription('Clears the cache')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// create some instances
		$finder = new Finder();
		$fs = new Filesystem();

		// build path to the rootdirectory
		$rootPath = __DIR__ . '/../..';

		$text = '<info>All done</info>';
		$output->writeln($text);
	}
}