<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

final class CreateMediaItemFromMovieUrl
{
    /** @var string */
    public $movieId;

    /** @var string */
    public $movieTitle;

    /** @var string */
    public $movieService;

    /** @var MediaFolder */
    public $mediaFolder;

    /** @var int */
    public $userId;

    /** @var MediaItem */
    private $mediaItem;

    /**
     * CreateMediaItemFromMovieUrl constructor.
     *
     * @param string $movieService
     * @param string $movieId
     * @param string $movieTitle
     * @param MediaFolder $mediaFolder
     * @param int $userId
     */
    public function __construct(
        string $movieService,
        string $movieId,
        string $movieTitle,
        MediaFolder $mediaFolder,
        int $userId
    ) {
        $this->movieService = $movieService;
        $this->movieTitle = $movieTitle;
        $this->movieId = $movieId;
        $this->mediaFolder = $mediaFolder;
        $this->userId = $userId;
    }

    /**
     * @return MediaItem
     */
    public function getMediaItem(): MediaItem
    {
        return $this->mediaItem;
    }

    /**
     * @param MediaItem $mediaItem
     */
    public function setMediaItem(MediaItem $mediaItem)
    {
        $this->mediaItem = $mediaItem;
    }
}
