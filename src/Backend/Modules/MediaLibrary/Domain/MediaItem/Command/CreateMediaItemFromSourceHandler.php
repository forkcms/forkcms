<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;

final class CreateMediaItemFromSourceHandler
{
    /** @var MediaItemRepository */
    protected $mediaItemRepository;

    /**
     * CreateMediaItemHandler constructor.
     *
     * @param MediaItemRepository $mediaItemRepository
     */
    public function __construct(MediaItemRepository $mediaItemRepository)
    {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    /**
     * @param CreateMediaItemFromSource $createMediaItemFromSource
     * @throws \Exception
     */
    public function handle(CreateMediaItemFromSource $createMediaItemFromSource)
    {
        /** @var MediaItem $mediaItem */
        $mediaItem = MediaItem::createFromSource(
            $createMediaItemFromSource->source,
            $createMediaItemFromSource->mediaFolder,
            $createMediaItemFromSource->userId
        );

        $this->mediaItemRepository->add($mediaItem);

        $createMediaItemFromSource->setMediaItem($mediaItem);
    }
}
