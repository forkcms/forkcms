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
 * This command is empty to remove the Fork cache
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class ClearCacheCommand extends ContainerAwareCommand
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

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir') . '/../';

        $foldersToClear = array(
            'src/Frontend/Cache/CachedTemplates/',
            'src/Frontend/Cache/Locale/',
            'src/Frontend/Cache/MinifiedCss/',
            'src/Frontend/Cache/MinifiedJs/',
            'src/Frontend/Cache/Navigation/',
            'src/Frontend/Cache/CompiledTemplates/',
            'src/Backend/Cache/Analytics/',
            'src/Backend/Cache/Cronjobs/',
            'src/Backend/Cache/Locale/',
            'src/Backend/Cache/Mailmotor/',
            'src/Backend/Cache/Navigation/',
            'src/Backend/Cache/CompiledTemplates/',
            'src/Backend/Cache/Logs/',
            'app/cache/',
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
