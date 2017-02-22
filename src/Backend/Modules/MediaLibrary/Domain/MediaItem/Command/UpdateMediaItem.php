<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateMediaItem
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $title;

    /**
     * @var MediaItem
     */
    public $mediaItem;

    /**
     * UpdateMediaItem constructor.
     *
     * @param MediaItem $mediaItem
     */
    public function __construct(MediaItem $mediaItem)
    {
        $this->mediaItem = $mediaItem;
        $this->title = $mediaItem->getTitle();
    }
}
