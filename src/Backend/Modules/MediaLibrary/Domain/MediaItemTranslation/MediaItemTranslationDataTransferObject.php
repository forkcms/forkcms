<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItemTranslation;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Common\Locale;
use Symfony\Component\Validator\Constraints as Assert;

final class MediaItemTranslationDataTransferObject
{
    /** @var MediaItemTranslation */
    private $mediaItemTranslationEntity;

    /** @var Locale */
    private $locale;

    /** @var MediaItem */
    private $mediaItem;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "err.FieldIsRequired")
     */
    public $title;

    /** @var string */
    public $caption;

    /** @var bool */
    public $hasCaptionLink;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"caption_link_is_required"})
     */
    public $captionLink;

    /** @var string */
    public $description;

    /** @var string */
    public $altText;

    public function __construct(MediaItemTranslation $mediaItemTranslation = null, Locale $locale = null)
    {
        $this->mediaItemTranslationEntity = $mediaItemTranslation;

        if (!$this->hasExistingMediaItemTranslation()) {
            $this->locale = $locale;

            return;
        }

        $this->mediaItem = $this->mediaItemTranslationEntity->getMediaItem();
        $this->locale = $this->mediaItemTranslationEntity->getLocale();
        $this->title = $this->mediaItemTranslationEntity->getTitle();
        $this->caption = $this->mediaItemTranslationEntity->getCaption();
        $this->hasCaptionLink = $this->mediaItemTranslationEntity->hasCaptionLink();
        $this->captionLink = $this->mediaItemTranslationEntity->getCaptionLink();
        $this->description = $this->mediaItemTranslationEntity->getDescription();
        $this->altText = $this->mediaItemTranslationEntity->getAltText();
    }

    public function getMediaItem(): ?MediaItem
    {
        return $this->mediaItem;
    }

    public function getMediaItemTranslationEntity(): MediaItemTranslation
    {
        return $this->mediaItemTranslationEntity;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function hasExistingMediaItemTranslation(): bool
    {
        return $this->mediaItemTranslationEntity instanceof MediaItemTranslation;
    }

    public function setMediaItem(MediaItem $mediaItem): void
    {
        $this->mediaItem = $mediaItem;
    }

    public function setLocale(Locale $locale): void
    {
        $this->locale = $locale;
    }
}
