<?php

namespace Backend\Modules\Sitemap\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Build sitemap
 * Example: "bin/console sitemap:build"
 */
class BuildSitemapCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setName('sitemap:build')
            ->setDescription('Build the sitemapindex and sitemaps');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->getContainer()->get('sitemap.builder')->buildCache();
    }
}
