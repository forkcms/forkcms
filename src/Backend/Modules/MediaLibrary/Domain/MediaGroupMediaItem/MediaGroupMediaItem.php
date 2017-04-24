<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem;

use Doctrine\ORM\Mapping as ORM;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use JsonSerializable;

/**
 * MediaGroup MediaItem
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItemRepository")
 */
class MediaGroupMediaItem implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @var MediaGroup
     *
     * @ORM\ManyToOne(
     *     targetEntity="Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup",
     *     inversedBy="connectedItems",
     *     cascade="persist"
     * )
     * @ORM\JoinColumn(
     *     name="mediaGroupId",
     *     referencedColumnName="id",
     *     onDelete="cascade"
     * )
     */
    protected $group;

    /**
     * @var MediaItem
     *
     * @ORM\ManyToOne(
     *     targetEntity="Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem",
     *     inversedBy="groups",
     *     cascade="persist",
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(
     *     name="mediaItemId",
     *     referencedColumnName="id",
     *     onDelete="cascade"
     * )
     */
    protected $item;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdOn;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $sequence;

    /**
     * @param MediaGroup $group
     * @param MediaItem $item
     * @param int $sequence
     */
    private function __construct(
        MediaGroup $group,
        MediaItem $item,
        int $sequence
    ) {
        $this->group = $group;
        $this->item = $item;
        $this->createdOn = new \DateTime();
        $this->sequence = $sequence;
    }

    /**
     * @param \Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup $group
     * @param \Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem $item
     * @param $sequence
     * @return MediaGroupMediaItem
     */
    public static function create(
        MediaGroup $group,
        MediaItem $item,
        int $sequence
    ) : MediaGroupMediaItem {
        return new self(
            $group,
            $item,
            $sequence
        );
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'item' => $this->item,
            'createdOn' => $this->createdOn->getTimestamp(),
            'sequence' => $this->sequence,
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return MediaGroup
     */
    public function getGroup(): MediaGroup
    {
        return $this->group;
    }

    /**
     * @return MediaItem
     */
    public function getItem(): MediaItem
    {
        return $this->item;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn(): \DateTime
    {
        return $this->createdOn;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence the sequence
     * @return MediaGroupMediaItem
     */
    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }
}
