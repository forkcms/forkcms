<?php

namespace Fork\ConsoleBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
		$text = '<info>All done</info>';
		$output->writeln($text);
	}
}