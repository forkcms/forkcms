<?php

namespace Backend\Modules\MediaLibrary\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Clear media library cache
 * Example: "bin/console media_library:cache:clear", will only clear all frontend MediaLibrary cached-thumbnails
 * Example: "bin/console media_library:cache:clear --all", will clear all MediaLibrary cached-thumbnails
 */
class CacheClearCommand extends ContainerAwareCommand
{
    /**
     * Should we clear all
     *
     * @var bool
     */
    protected $clearAll = false;

    protected function configure(): void
    {
        $this
            ->setName('media_library:cache:clear')
            ->setDescription('Clear all cached-thumbnails.')
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, the backend cached thumbnails will be cleared as well.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->checkOptions($input);
        $this->deleteCachedFolders();
        $output->writeln('<info>' . $this->getMessage() . '</info>');
    }

    private function checkOptions(InputInterface $input): void
    {
        if ($input->getOption('all')) {
            $this->clearAll = true;
        }
    }

    private function deleteCachedFolders(): void
    {
        $foldersToDelete = $this->getFoldersToDelete();
        foreach ($foldersToDelete as $folderPath) {
            $this->getContainer()->get('media_library.manager.file')->deleteFolder($folderPath);
        }
    }

    public function getMessage(): string
    {
        if ($this->clearAll) {
            return '[OK] Front- and backend cache cleared for "MediaLibrary".';
        }

        return '[OK] Frontend cache cleared for "MediaLibrary".';
    }

    public function getFoldersToDelete(): array
    {
        $finder = new Finder();
        $results = $finder->directories()->in(FRONTEND_FILES_PATH . '/Cache')
            ->name('media_library_*');

        if (!$this->clearAll) {
            $results->exclude('media_library_backend_thumbnail');
        }

        return array_map(
            function ($folder) {
                return $folder->getPathname();
            },
            iterator_to_array($results)
        );
    }
}
