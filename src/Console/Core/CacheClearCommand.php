<?php

namespace Console\Core;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command will clear all the cache
 */
class CacheClearCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('forkcms:cache:clear')
            ->setDescription('Clear the cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $this->removeFilesInFolder('/src/Frontend/Cache/CompiledTemplates', $io, 'frontend compiled templates');
        $this->removeFilesInFolder('/src/Frontend/Cache/Locale', $io, 'frontend cached locale');
        $this->removeFilesInFolder('/src/Frontend/Cache/MinifiedCss', $io, 'frontend minified css');
        $this->removeFilesInFolder('/src/Frontend/Cache/MinifiedJs', $io, 'frontend minified js');
        $this->removeFilesInFolder('/src/Frontend/Cache/Navigation', $io, 'frontend cached navigation');
        $this->removeFilesInFolder('/src/Frontend/Cache/Search', $io, 'frontend cached search results');

        $this->removeFilesInFolder('/src/Backend/Cache/CompiledTemplates', $io, 'backend compiled templates');
        $this->removeFilesInFolder('/src/Backend/Cache/Locale', $io, 'backend cached locale');
        $this->removeFilesInFolder('/src/Backend/Cache/MinifiedCss', $io, 'backend minified css');
        $this->removeFilesInFolder('/src/Backend/Cache/MinifiedJs', $io, 'backend minified js');

        $this->removeFilesInFolder('/var/cache', $io, 'general cache');

        $io->success('Cache is cleared');
    }

    /**
     * Remove the files in a given folder
     *
     * @param string $path
     * @param SymfonyStyle $io
     * @param string $name
     */
    private function removeFilesInFolder(string $path, SymfonyStyle $io, string $name): void
    {
        $fullPath = realpath(__DIR__ . '/../../..' . $path);

        // I use a rm-command because this is much faster then using the finder/filesystem-component
        $command = 'find %1$s ! -name ".gitignore" -type f ! -path *.svn/* -type f | xargs rm -f';
        shell_exec(sprintf($command, $fullPath));
        $io->comment(sprintf('Removed %1$s', $name));
    }
}
