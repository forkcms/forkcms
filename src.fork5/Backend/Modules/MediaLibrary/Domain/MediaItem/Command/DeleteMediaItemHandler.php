<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;

final class DeleteMediaItemHandler
{
    /** @var MediaItemRepository */
    private $mediaItemRepository;

    public function __construct(
        MediaItemRepository $mediaItemRepository
    ) {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    public function handle(DeleteMediaItem $deleteMediaItem): void
    {
        $this->mediaItemRepository->remove($deleteMediaItem->mediaItem);
    }
}
