<?php

namespace Frontend\Modules\MediaLibrary\Component;

use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * Frontend MediaItem
 * We use this component to adapt the MediaItem to be usable in Frontend.
 * F.e.: it adds the exact urls for "source" and all custom resolutions.
 */
class FrontendMediaItem
{
    /**
     * @var MediaItem
     */
    protected $mediaItem;

    /** @var string */
    protected $source;

    /**
     * Construct
     *
     * @param MediaItem $mediaItem
     */
    public function __construct(
        MediaItem $mediaItem
    ) {
        $this->mediaItem = $mediaItem;

        // We have a movie type
        if ($mediaItem->getType() === Type::MOVIE) {
            $this->addMovieSource();
        // All other files
        } else {
            $this->addUrl(
                'source'
            );
        }
    }

    /**
     * Add url - can be used in Frontend Templates
     * F.e.: $items.source, $items.small, $items.large, ...
     *
     * @param string $customKey   The customKey you want to use, f.e.: source, small, large, square
     * @param string|null $folderName  The resolution (f.e: 900x600-resize-100) folderName.
     */
    public function addUrl(
        $customKey,
        $folderName = null
    ) {
        if ($customKey === 'source') {
            // Define "source", "small", "large", ... with its url
            $this->$customKey = $this->mediaItem->getWebPath($folderName);
        } else {
            // Define "small_source", "large_source" ... with its url
            $this->{$customKey . '_source'} = $this->mediaItem->getWebPath($folderName);
        }

        if ($folderName !== null) {
            $this->{$customKey . '_resolution'} = $this->mediaItem->getWebPath($folderName);
        }
    }

    /**
     * Add movie source
     */
    protected function addMovieSource()
    {
        // Define url
        $url = '';

        // YouTube
        if ($this->mediaItem->getMime() === MediaItem::MIME_YOUTUBE) {
            // Define YouTube url
            $url = 'http://www.youtube.com/embed/';
        // Vimeo
        } elseif ($this->mediaItem->getMime() === MediaItem::MIME_VIMEO) {
            // Define Vimeo url
            $url = '//player.vimeo.com/video/';
        }

        if ($url) {
            // Set source
            $this->source = $url . $this->mediaItem->getUrl();
        }
    }

    /**
     * @return MediaItem
     */
    public function getMediaItem()
    {
        return $this->mediaItem;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}
