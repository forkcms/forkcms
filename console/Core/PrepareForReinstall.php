<?php

namespace Console\Core;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareForReinstall extends Command
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('core:prepare_for_reinstall')
            ->setDescription('Will remove all caches and configuration-files');
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
        $output->writeln('<info>All done</info>');
    }
}
