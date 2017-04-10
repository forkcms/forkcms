<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\DeleteMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MediaItemManager
{
    /** @var MediaItemRepository */
    private $mediaItemRepository;

    /** @var MessageBusSupportingMiddleware */
    private $commandBus;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param MediaItemRepository $mediaItemRepository
     * @param MessageBusSupportingMiddleware $commandBus
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        MediaItemRepository $mediaItemRepository,
        MessageBusSupportingMiddleware $commandBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->mediaItemRepository = $mediaItemRepository;
        $this->commandBus = $commandBus;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param MediaItem $mediaItem
     * @return DeleteMediaItem
     */
    public function delete(MediaItem $mediaItem): DeleteMediaItem
    {
        /** @var DeleteMediaItem $deleteMediaItem */
        $deleteMediaItem = new DeleteMediaItem($mediaItem);

        // Handle the MediaItem delete
        $this->commandBus->handle($deleteMediaItem);

        return $deleteMediaItem;
    }

    /**
     * @param bool $deleteAll
     * @return int
     */
    public function deleteAll($deleteAll = false): int
    {
        /** @var array $mediaItems */
        $mediaItems = $this->mediaItemRepository->findAll();
        $counter = 0;

        // Loop all media items
        foreach ($mediaItems as $mediaItem) {
            if (!$deleteAll && $mediaItem->getGroups()->count() > 0) {
                continue;
            }

            $this->delete($mediaItem);
            $counter ++;
        }

        return $counter;
    }
}
