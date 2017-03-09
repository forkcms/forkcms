<?php

namespace Backend\Modules\MediaLibrary\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\DeleteMediaItem;

/**
 * Delete all media items Console Command
 * Example: "app/console media_library:delete:items", will delete all MediaItems and all its connections and files
 */
class MediaItemDeleteAllCommand extends ContainerAwareCommand
{
    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('media_library:delete:items')
            ->setDescription('Delete all MediaLibrary items.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Write output
        $output->writeln(
            '<info>-Started deleting media items.</info>'
        );

        /** @var array $mediaItems */
        $mediaItems = $this->getContainer()->get('media_library.repository.item')->findAll();

        // Loop all media items
        foreach ($mediaItems as $mediaItem) {
            /** @var DeleteMediaItem $deleteMediaItem */
            $deleteMediaItem = new DeleteMediaItem($mediaItem);

            // Handle the MediaItem delete
            $this->getContainer()->get('command_bus')->handle($deleteMediaItem);
            $this->getContainer()->get('event_dispatcher')->dispatch(
                MediaItemDeleted::EVENT_NAME,
                new MediaItemDeleted($deleteMediaItem->mediaItem)
            );
        }

        // Write output
        $output->writeln(
            '<info>-Finished deleting media items.</info>'
        );
    }
}
