<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * MediaGroup
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaGroup
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @var Type
     *
     * @ORM\Column(type="media_group_type")
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $editedOn;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem",
     *     mappedBy="group",
     *     cascade={"persist", "merge", "remove", "detach"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"sequence": "ASC"})
     */
    protected $connectedItems;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $numberOfConnectedItems;

    /**
     * MediaGroup constructor.
     *
     * @param UuidInterface $id
     * @param Type $type
     */
    private function __construct(
        UuidInterface $id,
        Type $type
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->connectedItems = new ArrayCollection();
    }

    /**
     * Create
     *
     * @param Type $type
     * @return MediaGroup
     */
    public static function create(
        Type $type
    ) : MediaGroup {
        return new self(
            Uuid::uuid4(),
            $type
        );
    }

    /**
     * Create from id
     *
     * @param UuidInterface $id
     * @param Type $type
     * @return MediaGroup
     */
    public static function createFromId(
        UuidInterface $id,
        Type $type
    ) : MediaGroup {
        return new self(
            $id,
            $type
        );
    }

    /**
     * @param MediaGroupDataTransferObject $mediaGroupDataTransferObject
     * @return MediaGroup
     */
    public static function fromDataTransferObject(MediaGroupDataTransferObject $mediaGroupDataTransferObject): MediaGroup
    {
        if ($mediaGroupDataTransferObject->hasExistingMediaGroup()) {
            return $mediaGroupDataTransferObject
                ->getMediaGroupEntity()
                ->updateFromDataTransferObject($mediaGroupDataTransferObject);
        }

        if ($mediaGroupDataTransferObject->id === null) {
            return self::create(
                $mediaGroupDataTransferObject->type
            );
        }

        return new self(
            $mediaGroupDataTransferObject->id,
            $mediaGroupDataTransferObject->type
        );
    }

    /**
     * @param MediaGroupDataTransferObject $mediaGroupDataTransferObject
     * @return MediaGroup
     */
    private function updateFromDataTransferObject(MediaGroupDataTransferObject $mediaGroupDataTransferObject)
    {
        // Remove all previous connected items
        if ($mediaGroupDataTransferObject->removeAllPreviousConnectedMediaItems) {
            $this->getConnectedItems()->clear();
        }

        return $this;
    }

    /**
     * To array
     *
     * @return array
     */
    public function __toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'editedOn' => ($this->editedOn) ? $this->editedOn->getTimestamp() : null,
            'connectedItems' => $this->connectedItems->map(
                function (MediaGroupMediaItem $connectedItem) {
                    return $connectedItem->__toArray();
                }
            ),
        ];
    }

    /**
     * Gets the value of id.
     *
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Gets the value of type.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * Gets the value of editedOn.
     *
     * @return \DateTime
     */
    public function getEditedOn(): \DateTime
    {
        return $this->editedOn;
    }

    /**
     * Gets the value of connectedItems.
     *
     * @return Collection
     */
    public function getConnectedItems(): Collection
    {
        return $this->connectedItems;
    }

    /**
     * @return Collection
     */
    public function getConnectedMediaItems(): Collection
    {
        return $this->connectedItems->map(function (MediaGroupMediaItem $connectedItem): MediaItem {
            return $connectedItem->getItem();
        });
    }

    /**
     * @return MediaItem|null
     */
    public function getFirstConnectedMediaItem()
    {
        $connectedMediaItems = $this->getConnectedMediaItems();

        return ($connectedMediaItems instanceof Collection) ? $this->getConnectedMediaItems()->first() : null;
    }

    /**
     * @return bool
     */
    public function hasConnectedItems(): bool
    {
        return $this->numberOfConnectedItems > 0;
    }

    /**
     * @return int
     */
    public function getNumberOfConnectedItems(): int
    {
        return $this->numberOfConnectedItems;
    }

    /**
     * @param string $mediaItemId
     * @return MediaGroupMediaItem
     */
    public function getConnectedItemByMediaItemId(string $mediaItemId): MediaGroupMediaItem
    {
        /** @var MediaGroupMediaItem $mediaGroupMediaItem */
        foreach ($this->connectedItems->toArray() as $mediaGroupMediaItem) {
            if ($mediaGroupMediaItem->getItem()->getId() === $mediaItemId) {
                return $mediaGroupMediaItem;
            }
        }
    }

    /**
     * @param MediaGroupMediaItem $connectedItem
     * @return MediaGroup
     */
    public function addConnectedItem(MediaGroupMediaItem $connectedItem): MediaGroup
    {
        $this->connectedItems->add($connectedItem);

        // This is required, otherwise, doctrine thinks the entity hasn't been changed
        $this->setNumberOfConnectedItems();

        return $this;
    }

    /**
     * @param MediaGroupMediaItem $connectedItem
     * @return MediaGroup
     */
    public function removeConnectedItem(MediaGroupMediaItem $connectedItem): MediaGroup
    {
        $this->connectedItems->removeElement($connectedItem);

        // This is required, otherwise, doctrine thinks the entity hasn't been changed
        $this->setNumberOfConnectedItems();

        return $this;
    }

    private function setNumberOfConnectedItems()
    {
        $this->numberOfConnectedItems = $this->connectedItems->count();
    }

    /**
     * Gets the value of connectedItems.
     *
     * @return array
     */
    public function getIdsForConnectedItems(): array
    {
        return array_map(function ($connectedItem) {
            return $connectedItem->getItem()->getId();
        }, $this->connectedItems->toArray());
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->editedOn = new \Datetime();
        $this->setNumberOfConnectedItems();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->editedOn = new \Datetime();
        $this->setNumberOfConnectedItems();
    }
}
