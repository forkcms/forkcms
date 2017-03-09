<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\DeleteMediaItem;

final class MediaItemDeleteHandler
{
    /** @var MediaItemRepository */
    private $mediaItemRepository;

    /**
     * MediaItemDeleteHandler constructor.
     *
     * @param MediaItemRepository $mediaItemRepository
     */
    public function __construct(
        MediaItemRepository $mediaItemRepository
    ) {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    /**
     * @param MediaItemDelete $deleteMediaItem
     */
    public function handle(MediaItemDelete $deleteMediaItem)
    {
        $this->mediaItemRepository->remove($deleteMediaItem->mediaItem);
    }
}
