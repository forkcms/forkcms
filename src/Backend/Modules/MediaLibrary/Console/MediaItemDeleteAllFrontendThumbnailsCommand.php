<?php

namespace Backend\Modules\MediaLibrary\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete generated folder Command
 * Example: "app/console media_library:delete:frontend",
 * will delete the Generated folder which contains all generated images for the frontend.
 */
class MediaItemDeleteAllFrontendThumbnailsCommand extends ContainerAwareCommand
{
    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('media_library:delete:frontend')
            ->setDescription('Delete frontend generated images folder.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $frontendPath = $this->getContainer()->get('media_library.storage.local')->getUploadRootDir('frontend');

        // Delete Frontend Generated folder
        $this->getContainer()->get('media_library.manager.file')->deleteFolder($frontendPath);

        // Write output
        $output->writeln('<info>Deleted the folder: "' . $frontendPath . '"</info>');
    }
}
