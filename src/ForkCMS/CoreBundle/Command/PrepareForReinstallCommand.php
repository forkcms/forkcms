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

        $cacheClarer = $this->getContainer()->get('forkcms_core.cache_clearer');
        $cacheClarer->clearInstallCache();
        $cacheClarer->clearFrontendCache();
        $cacheClarer->clearBackendCache();
        $cacheClarer->removeParametersFile();

        $output->writeln('All done! Ready for reinstall.');
    }
}
