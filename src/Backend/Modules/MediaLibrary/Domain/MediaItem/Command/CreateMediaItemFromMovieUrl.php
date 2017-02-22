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

    /** @var integer */
    public $userId;

    /** @var MediaItem */
    private $mediaItem;

    /**
     * CreateMediaItemFromMovieUrl constructor.
     *
     * @param $movieService
     * @param $movieId
     * @param $movieTitle
     * @param $mediaFolder
     * @param $userId
     */
    public function __construct(
        $movieService,
        $movieId,
        $movieTitle,
        MediaFolder $mediaFolder,
        $userId
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
    public function getMediaItem()
    {
        return $this->mediaItem;
    }

    /**
     * @param MediaItem $mediaItem
     */
    public function setMediaItem($mediaItem)
    {
        $this->mediaItem = $mediaItem;
    }
}
