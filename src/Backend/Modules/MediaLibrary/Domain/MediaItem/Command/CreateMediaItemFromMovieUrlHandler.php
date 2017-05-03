<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;

final class CreateMediaItemFromMovieUrlHandler
{
    /** @var MediaItemRepository */
    protected $mediaItemRepository;

    public function __construct(MediaItemRepository $mediaItemRepository)
    {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    public function handle(CreateMediaItemFromMovieUrl $createMediaItemFromMovieUrl): void
    {
        /** @var MediaItem $mediaItem */
        $mediaItem = MediaItem::createFromMovieUrl(
            $createMediaItemFromMovieUrl->source,
            $createMediaItemFromMovieUrl->movieId,
            $createMediaItemFromMovieUrl->movieTitle,
            $createMediaItemFromMovieUrl->mediaFolder,
            $createMediaItemFromMovieUrl->userId
        );

        $createMediaItemFromMovieUrl->setMediaItem($mediaItem);

        $this->mediaItemRepository->add($mediaItem);
    }
}
