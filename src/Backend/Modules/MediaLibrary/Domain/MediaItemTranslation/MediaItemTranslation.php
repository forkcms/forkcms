<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItemTranslation;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Common\Doctrine\Entity\Meta;
use Common\Locale;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="MediaItemTranslation")
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaItemTranslation\MediaItemTranslationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class MediaItemTranslation
{
    /**
     * @var Locale
     *
     * @ORM\Id()
     * @ORM\Column(type="locale", name="locale")
     */
    private $locale;

    /**
     * @var MediaItem
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem", inversedBy="translations")
     * @ORM\JoinColumn(name="mediaItemId", referencedColumnName="id")
     */
    private $mediaItem;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var null|string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $caption;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $altText;

    /**
     * @var null|string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __construct(
        Locale $locale,
        MediaItem $mediaItem,
        string $title,
        string $caption = null,
        string $link = null,
        string $description = null,
        string $altText = null
    ) {
        $this->locale = $locale;
        $this->mediaItem = $mediaItem;
        $this->title = $title;
        $this->caption = $caption;
        $this->link = $caption;
        $this->description = $description;
        $this->altText = $altText;

        $this->mediaItem->addTranslation($this);
    }

    public static function fromDataTransferObject(
        MediaItemTranslationDataTransferObject $mediaItemTranslationDataTransferObject
    ): self {
        // Update the MediaItemTranslation
        if ($mediaItemTranslationDataTransferObject->hasExistingMediaItemTranslation()) {
            $mediaItemTranslation = $mediaItemTranslationDataTransferObject->getMediaItemTranslationEntity();

            $mediaItemTranslation->title = $mediaItemTranslationDataTransferObject->title;
            $mediaItemTranslation->caption = $mediaItemTranslationDataTransferObject->caption;
            $mediaItemTranslation->link = $mediaItemTranslationDataTransferObject->link;
            $mediaItemTranslation->link = ($mediaItemTranslationDataTransferObject->hasLink)
                ? self::stripLinkToUrl($mediaItemTranslationDataTransferObject->link) : null;
            $mediaItemTranslation->description = $mediaItemTranslationDataTransferObject->description;
            $mediaItemTranslation->altText = $mediaItemTranslationDataTransferObject->altText;

            return $mediaItemTranslation;
        }

        // Create new MediaItemTranslation
        $mediaItemTranslation = new self(
            $mediaItemTranslationDataTransferObject->getLocale(),
            $mediaItemTranslationDataTransferObject->getMediaItem(),
            $mediaItemTranslationDataTransferObject->title,
            $mediaItemTranslationDataTransferObject->caption,
            self::stripLinkToUrl($mediaItemTranslationDataTransferObject->link),
            $mediaItemTranslationDataTransferObject->description,
            $mediaItemTranslationDataTransferObject->altText
        );

        return $mediaItemTranslation;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getDataTransferObject(): MediaItemTranslationDataTransferObject
    {
        $dataTransferObject = new MediaItemTranslationDataTransferObject($this, $this->locale);
        $dataTransferObject->title = $this->title;
        $dataTransferObject->caption = $this->caption;
        $dataTransferObject->description = $this->description;
        $dataTransferObject->altText = $this->altText;

        return $dataTransferObject;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getMediaItem(): MediaItem
    {
        return $this->mediaItem;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function hasLink(): bool
    {
        return $this->link !== null;
    }

    public static function stripLinkToUrl(?string $link): ?string
    {
        if ($link === null) {
            return null;
        }

        $link = str_replace(SITE_URL, '', $link);

        return $link;
    }
}
