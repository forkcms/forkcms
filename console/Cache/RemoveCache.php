<?php

namespace Console\Cache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class RemoveCache extends Command
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('cache:remove')
            ->setDescription('Clears the cache');
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
        // create some instances
        $finder = new Finder();
        $fs = new Filesystem();

        // build path to the rootdirectory
        $rootPath = __DIR__ . '/../..';

        // remove the installers cache
        $finder->files()->in($rootPath . '/install/cache');
        $finder->files()->notName('installed.txt');
        $fs->remove($finder);
        $output->writeln('<comment>Installer cache is cleared</comment>');

        // remove the frontend cache
        $finder->files()->in($rootPath . '/frontend/cache');
        $fs->remove($finder);
        $output->writeln('<comment>Frontend cache is cleared</comment>');

        // remove the backend cache
        $finder->files()->in($rootPath . '/backend/cache');
        $fs->remove($finder);
        $output->writeln('<comment>Backend cache is cleared</comment>');

        $output->writeln('<info>All done</info>');
    }
}
