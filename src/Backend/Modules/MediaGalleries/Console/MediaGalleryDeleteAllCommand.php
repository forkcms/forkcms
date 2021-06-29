<?php

namespace Backend\Modules\MediaGalleries\Console;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\DeleteMediaGallery;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete media galleries Console Command
 * Example: "bin/console media_galleries:delete:galleries", will delete all galleries
 * Example: "bin/console media_galleries:delete:galleries --delete-media-items", will delete all galleries and all MediaItem entities
 */
class MediaGalleryDeleteAllCommand extends ContainerAwareCommand
{
    /**
     * The MediaGroupMediaItem connections are always deleted,
     * but should we delete the source MediaItem items as well?
     *
     * @var bool
     */
    protected $deleteMediaItems = false;

    protected function configure(): void
    {
        $this
            ->setName('media_galleries:delete:galleries')
            ->setDescription('Delete media galleries.')
            ->addOption(
                'delete-media-items',
                null,
                InputOption::VALUE_NONE,
                'If set, all connected MediaItems will be deleted as well from the library.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>-Started deleting media galleries.</info>');

        $this->checkOptions($input);
        $this->deleteMediaGalleries();

        $output->writeln('<info>-Finished deleting media galleries.</info>');
    }

    private function checkOptions(InputInterface $input): void
    {
        if ($input->getOption('delete-media-items')) {
            $this->deleteMediaItems = true;
        }
    }

    private function deleteMediaGalleries(): void
    {
        /** @var array $mediaGalleries */
        $mediaGalleries = $this->getContainer()->get(MediaGalleryRepository::class)->findAll();

        if (empty($mediaGalleries)) {
            return;
        }

        // Loop all media galleries
        foreach ($mediaGalleries as $mediaGallery) {
            $this->getContainer()->get('command_bus')->handle(
                new DeleteMediaGallery($mediaGallery),
                $this->deleteMediaItems
            );
        }
    }
}
