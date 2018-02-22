<?php

namespace App\Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use App\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use App\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;

final class CreateMediaItemFromLocalStorageTypeHandler
{
    /** @var MediaItemRepository */
    protected $mediaItemRepository;

    public function __construct(MediaItemRepository $mediaItemRepository)
    {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    public function handle(CreateMediaItemFromLocalStorageType $createMediaItemFromLocalStorageType): void
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
