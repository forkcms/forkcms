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

/**
 * This command is empties the Fork cache
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
        $cacheClearer = $this->getContainer()->get('forkcms_core.cache_clearer');
        $cacheClearer->clearFrontendCache();
        $cacheClearer->clearBackendCache();
        $cacheClearer->clearAppCache();
        $output->writeln('All done! Cache files removed.');
    }
}
