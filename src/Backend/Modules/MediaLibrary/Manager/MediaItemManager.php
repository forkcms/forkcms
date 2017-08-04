<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\DeleteMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

class MediaItemManager
{
    /** @var MediaItemRepository */
    private $mediaItemRepository;

    /** @var MessageBusSupportingMiddleware */
    private $commandBus;

    public function __construct(
        MediaItemRepository $mediaItemRepository,
        MessageBusSupportingMiddleware $commandBus
    ) {
        $this->mediaItemRepository = $mediaItemRepository;
        $this->commandBus = $commandBus;
    }

    public function delete(MediaItem $mediaItem): DeleteMediaItem
    {
        $deleteMediaItem = new DeleteMediaItem($mediaItem);

        // Handle the MediaItem delete
        $this->commandBus->handle($deleteMediaItem);

        return $deleteMediaItem;
    }

    public function deleteAll(bool $deleteAll = false): int
    {
        $counter = 0;

        // Loop all media items
        foreach ($this->mediaItemRepository->findAll() as $mediaItem) {
            if (!$deleteAll && $mediaItem->getGroups()->count() > 0) {
                continue;
            }

            $this->delete($mediaItem);
            ++$counter;
        }

        return $counter;
    }
}
