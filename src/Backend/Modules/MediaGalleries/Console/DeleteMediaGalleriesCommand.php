<?php

namespace Backend\Modules\MediaGalleries\Console;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\DeleteMediaGallery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete media galleries Console Command
 * Example: "app/console media_galleries:delete:galleries", will delete all galleries
 * Example: "app/console media_galleries:delete:galleries --delete-media-items", will delete all galleries and all MediaItem entities
 */
class DeleteMediaGalleriesCommand extends ContainerAwareCommand
{
    /**
     * The MediaGroupMediaItem connections are always deleted,
     * but should we delete the source MediaItem items as well?
     *
     * @var boolean
     */
    protected $deleteMediaItems = false;

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('media_galleries:delete:galleries')
            ->setDescription('Delete media galleries.')
            ->addOption(
                'delete-media-items',
                null,
                InputOption::VALUE_NONE,
                'If set, all connected MediaItems will be deleted as well from the library.'
            )
        ;
    }

    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Write output
        $output->writeln(
            '<info>-Started deleting media galleries.</info>'
        );

        // If set overwrite "deleteMediaItems"
        if ($input->getOption('delete-media-items')) {
            $this->deleteMediaItems = true;
        }

        /** @var array $mediaGalleries */
        $mediaGalleries = $this->getContainer()->get('media_galleries.repository.gallery')->findAll();

        // Loop all media galleries
        foreach ($mediaGalleries as $mediaGallery) {
            /** @var DeleteMediaGallery $deleteMediaGallery */
            $deleteMediaGallery = new DeleteMediaGallery($mediaGallery);

            // Handle the MediaGallery delete
            $this->getContainer()->get('command_bus')->handle(
                $deleteMediaGallery,
                $this->deleteMediaItems
            );
        }

        // Write output
        $output->writeln(
            '<info>-Finished deleting media galleries.</info>'
        );

        return;
    }
}
