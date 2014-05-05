<?php

namespace ForkCMS\CoreBundle\Command;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This command prepares Fork for a fresh install
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class PrepareForReinstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fork:install:prepare')
            ->setDescription('Prepares the Fork install to be reïnstalled')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder;
        $fs = new Filesystem;

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir') . '/../';

        $foldersToClear = array(
            'src/Install/Cache/',
            'src/Frontend/Cache/CachedTemplates/',
            'src/Frontend/Cache/Config/',
            'src/Frontend/Cache/Locale/',
            'src/Frontend/Cache/MinifiedCss/',
            'src/Frontend/Cache/MinifiedJs/',
            'src/Frontend/Cache/Navigation/',
            'src/Frontend/Cache/CompiledTemplates/',
            'src/Backend/Cache/Analytics/',
            'src/Backend/Cache/Config/',
            'src/Backend/Cache/Cronjobs/',
            'src/Backend/Cache/Locale/',
            'src/Backend/Cache/Mailmotor/',
            'src/Backend/Cache/Navigation/',
            'src/Backend/Cache/CompiledTemplates/',
            'src/Backend/Cache/Logs/',
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
