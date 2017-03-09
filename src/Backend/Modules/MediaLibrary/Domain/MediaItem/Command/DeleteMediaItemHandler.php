<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\DeleteMediaItem;

final class DeleteMediaItemHandler
{
    /** @var MediaItemRepository */
    private $mediaItemRepository;

    /**
     * DeleteMediaItemHandler constructor.
     *
     * @param MediaItemRepository $mediaItemRepository
     */
    public function __construct(
        MediaItemRepository $mediaItemRepository
    ) {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    /**
     * @param DeleteMediaItem $deleteMediaItem
     */
    public function handle(DeleteMediaItem $deleteMediaItem)
    {
        $this->mediaItemRepository->remove($deleteMediaItem->mediaItem);
    }
}
