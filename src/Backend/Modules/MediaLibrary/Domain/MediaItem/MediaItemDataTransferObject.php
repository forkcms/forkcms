<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Core\Language\Language;
use Backend\Core\Language\Locale;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItemTranslation\MediaItemTranslation;
use Backend\Modules\MediaLibrary\Domain\MediaItemTranslation\MediaItemTranslationDataTransferObject;
use Doctrine\Common\Collections\ArrayCollection;

class MediaItemDataTransferObject
{
    /** @var MediaItem */
    protected $mediaItemEntity;

    /** @var MediaFolder|null */
    public $folder;

    /** @var string */
    public $url;

    /** @var int */
    public $userId;

    /**
     * @var MediaItemTranslationDataTransferObject[]|ArrayCollection
     */
    public $translations;

    public function __construct(MediaItem $mediaItem = null)
    {
        $this->mediaItemEntity = $mediaItem;
        $this->translations = new ArrayCollection();

        if (!$this->hasExistingMediaItem()) {
            foreach (array_keys(Language::getWorkingLanguages()) as $workingLanguage) {
                $this->translations->set(
                    $workingLanguage,
                    new MediaItemTranslationDataTransferObject(
                        null,
                        Locale::fromString($workingLanguage)
                    )
                );
            }

            return;
        }

        $this->folder = $this->mediaItemEntity->getFolder();
        $this->url = $this->mediaItemEntity->getUrl();
        $this->userId = $this->mediaItemEntity->getUserId();

        /** @var MediaItemTranslation $translation */
        foreach ($mediaItem->getTranslations() as $translation) {
            $this->translations->set((string) $translation->getLocale(), $translation->getDataTransferObject());
        }
    }

    public function getMediaItemEntity(): MediaItem
    {
        return $this->mediaItemEntity;
    }

    public function hasExistingMediaItem(): bool
    {
        return $this->mediaItemEntity instanceof MediaItem;
    }

    public function setMediaItemEntity(MediaItem $mediaItemEntity): void
    {
        $this->mediaItemEntity = $mediaItemEntity;
    }
}
