<?php

namespace Common\Tool\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class PrepareForReinstall extends Command
{
    protected function configure()
    {
        $this
            ->setName('install:prepare')
            ->setDescription('Prepares the Fork install to be reïnstalled')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder;
        $fs = new Filesystem;

        // @todo: fetch the root dir from the container
        $rootDir = __DIR__ . '/../../../';

        $foldersToClear = array(
            'Install/Cache/',
            'Frontend/Cache/CachedTemplates/',
            'Frontend/Cache/Config/',
            'Frontend/Cache/Locale/',
            'Frontend/Cache/MinifiedCss/',
            'Frontend/Cache/MinifiedJs/',
            'Frontend/Cache/Navigation/',
            'Frontend/Cache/CompiledTemplates/',
            'Backend/Cache/Analytics/',
            'Backend/Cache/Config/',
            'Backend/Cache/Cronjobs/',
            'Backend/Cache/Locale/',
            'Backend/Cache/Mailmotor/',
            'Backend/Cache/Navigation/',
            'Backend/Cache/CompiledTemplates/',
            'Backend/Cache/Logs/',
        );

        foreach ($foldersToClear as $folder) {
            if ($fs->exists($rootDir . $folder)) {
                foreach ($finder->files()->in($rootDir . $folder) as $file) {
                    $fs->remove($file->getRealPath());
                }
            }
        }

        $filesToClear = array(
            '../app/config/parameters.yml',
        );

        foreach ($filesToClear as $file) {
            if ($fs->exists($rootDir . $file)) {
                $fs->remove($rootDir . $file);
            }
        }

        $output->writeln('All done! Ready for reinstall.');
    }
}
