<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\Exception\MediaGroupMediaItemNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Countable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * MediaGroup
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaGroup implements JsonSerializable, Countable
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
     * @var Collection<MediaGroupMediaItem>
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

    private function __construct(
        UuidInterface $id,
        Type $type
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->connectedItems = new ArrayCollection();
    }

    public static function create(
        Type $type
    ): self {
        return new self(
            Uuid::uuid4(),
            $type
        );
    }

    public static function createFromId(
        UuidInterface $id,
        Type $type
    ): self {
        return new self(
            $id,
            $type
        );
    }

    public static function fromDataTransferObject(
        MediaGroupDataTransferObject $mediaGroupDataTransferObject
    ): self {
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

    private function updateFromDataTransferObject(MediaGroupDataTransferObject $mediaGroupDataTransferObject): self
    {
        // Remove all previous connected items
        if ($mediaGroupDataTransferObject->removeAllPreviousConnectedMediaItems) {
            $this->getConnectedItems()->clear();
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'editedOn' => $this->editedOn ? $this->editedOn->getTimestamp() : null,
            'connectedItems' => $this->connectedItems->toArray(),
        ];
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getEditedOn(): \DateTime
    {
        return $this->editedOn;
    }

    public function getConnectedItems(): Collection
    {
        return $this->connectedItems;
    }

    public function getConnectedMediaItems(): Collection
    {
        return $this->connectedItems->map(
            function (MediaGroupMediaItem $connectedItem): MediaItem {
                return $connectedItem->getItem();
            }
        );
    }

    public function getFirstConnectedMediaItem(): ?MediaItem
    {
        $connectedMediaItems = $this->getConnectedMediaItems();

        return $connectedMediaItems->isEmpty() ? null : $connectedMediaItems->first();
    }

    public function hasConnectedItems(): bool
    {
        return $this->numberOfConnectedItems > 0;
    }

    public function getNumberOfConnectedItems(): int
    {
        return $this->numberOfConnectedItems;
    }

    public function getConnectedItemByMediaItemId(string $mediaItemId): MediaGroupMediaItem
    {
        /** @var MediaGroupMediaItem $mediaGroupMediaItem */
        foreach ($this->connectedItems->toArray() as $mediaGroupMediaItem) {
            if ($mediaGroupMediaItem->getItem()->getId() === $mediaItemId) {
                return $mediaGroupMediaItem;
            }
        }

        throw MediaGroupMediaItemNotFound::forMediaItemId($mediaItemId);
    }

    public function addConnectedItem(MediaGroupMediaItem $connectedItem): MediaGroup
    {
        $this->connectedItems->add($connectedItem);

        // This is required, otherwise, doctrine thinks the entity hasn't been changed
        $this->setNumberOfConnectedItems();

        return $this;
    }

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

    public function getIdsForConnectedItems(): array
    {
        return array_map(
            function ($connectedItem) {
                return $connectedItem->getItem()->getId();
            },
            $this->connectedItems->toArray()
        );
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->editedOn = new \DateTime();
        $this->setNumberOfConnectedItems();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->editedOn = new \DateTime();
        $this->setNumberOfConnectedItems();
    }

    public function count(): int
    {
        return $this->connectedItems->count();
    }
}
