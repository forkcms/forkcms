<?php

namespace Fork\Blog\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FooCache extends Command
{
	/**
	 * Configure the command
	 */
	protected function configure()
	{
		$this->setName('blog:foo')
			->setDescription('foo');
	}

	/**
	 * Execute the command
	 *
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
	}
}
