<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem;

use Doctrine\ORM\Mapping as ORM;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;

/**
 * MediaGroup MediaItem
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItemRepository")
 */
class MediaGroupMediaItem
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
     *      targetEntity="Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup",
     *      inversedBy="connectedItems",
     *      cascade="persist"
     * )
     * @ORM\JoinColumn(
     *      name="mediaGroupId",
     *      referencedColumnName="id",
     *      onDelete="cascade"
     * )
     */
    protected $group;

    /**
     * @var MediaItem
     *
     * @ORM\ManyToOne(
     *      targetEntity="Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem",
     *      inversedBy="groups",
     *      cascade="persist"
     * )
     * @ORM\JoinColumn(
     *      name="mediaItemId",
     *      referencedColumnName="id",
     *      onDelete="cascade"
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
     * With $publishOn you can choose when this MediaItem will become visible.
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $publishOn;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $sequence;

    /**
     * Construct
     *
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
        $this->publishOn = new \DateTime();
        $this->sequence = (int) $sequence;
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
    ) {
        return new self(
            $group,
            $item,
            $sequence
        );
    }

    /**
     * To array
     *
     * @return array
     */
    public function __toArray()
    {
        return [
            'id' => $this->id,
            'item' => $this->item->__toArray(),
            'createdOn' => $this->createdOn->getTimestamp(),
            'publishOn' => $this->publishOn->getTimestamp(),
            'sequence' => $this->sequence,
        ];
    }

    /**
     * Gets the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of group.
     *
     * @return MediaGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Gets the value of item.
     *
     * @return MediaItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Gets the value of createdOn.
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Gets the With $publishOn you can choose when this image will be visible.
     *
     * @return \DateTime
     */
    public function getPublishOn()
    {
        return $this->publishOn;
    }

    /**
     * Gets the value of sequence.
     *
     * @return integer
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Sets the value of sequence.
     *
     * @param int $sequence the sequence
     * @return self
     */
    public function setSequence(int $sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }
}
