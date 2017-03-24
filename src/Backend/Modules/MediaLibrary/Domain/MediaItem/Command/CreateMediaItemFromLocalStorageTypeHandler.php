<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;

final class CreateMediaItemFromLocalStorageTypeHandler
{
    /** @var MediaItemRepository */
    protected $mediaItemRepository;

    /**
     * @param MediaItemRepository $mediaItemRepository
     */
    public function __construct(MediaItemRepository $mediaItemRepository)
    {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    /**
     * @param CreateMediaItemFromLocalStorageType $createMediaItemFromLocalStorageType
     * @throws \Exception
     */
    public function handle(CreateMediaItemFromLocalStorageType $createMediaItemFromLocalStorageType)
    {
        /** @var MediaItem $mediaItem */
        $mediaItem = MediaItem::createFromLocalStorageType(
            $createMediaItemFromLocalStorageType->path,
            $createMediaItemFromLocalStorageType->mediaFolder,
            $createMediaItemFromLocalStorageType->userId
        );

        $this->mediaItemRepository->add($mediaItem);

        $createMediaItemFromLocalStorageType->setMediaItem($mediaItem);
    }
}
