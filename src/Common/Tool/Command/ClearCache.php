<?php

namespace Common\Tool\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class ClearCache extends Command
{
    protected function configure()
    {
        $this
            ->setName('fork:cache:clear')
            ->setDescription('Clear the cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder;
        $fs = new Filesystem;

        // @todo: fetch the root dir from the container
        $rootDir = __DIR__ . '/../../../';

        $foldersToClear = array(
            'Frontend/Cache/CachedTemplates/',
            'Frontend/Cache/Locale/',
            'Frontend/Cache/MinifiedCss/',
            'Frontend/Cache/MinifiedJs/',
            'Frontend/Cache/Navigation/',
            'Frontend/Cache/CompiledTemplates/',
            'Backend/Cache/Analytics/',
            'Backend/Cache/Cronjobs/',
            'Backend/Cache/Locale/',
            'Backend/Cache/Mailmotor/',
            'Backend/Cache/Navigation/',
            'Backend/Cache/CompiledTemplates/',
            'Backend/Cache/Logs/',
            '../app/cache/',
        );

        foreach ($foldersToClear as $folder) {
            if ($fs->exists($rootDir . $folder)) {
                foreach ($finder->files()->in($rootDir . $folder) as $file) {
                    $fs->remove($file->getRealPath());
                }
            }
        }

        $output->writeln('All done! Cache files removed.');
    }
}
