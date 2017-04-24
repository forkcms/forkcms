<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Common\Uri;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;

/**
 * MediaFolder
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaFolder implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var MediaFolder|null
     *
     * @ORM\ManyToOne(
     *     targetEntity="Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder",
     *     inversedBy="children",
     *     cascade="persist"
     * )
     * @ORM\JoinColumn(
     *     name="parentMediaFolderId",
     *     referencedColumnName="id",
     *     onDelete="cascade"
     * )
     */
    protected $parent;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $userId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdOn;

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
     *     targetEntity="Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem",
     *     mappedBy="folder",
     *     cascade={"persist","merge"},
     *     orphanRemoval=true
     * )
     */
    protected $items;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder",
     *     mappedBy="parent",
     *     cascade={"persist","merge"},
     *     orphanRemoval=true
     * )
     */
    protected $children;

    /**
     * @param string $name The name of this folder.
     * @param MediaFolder|null $parent The parent of this folder, can be NULL.
     * @param int $userId The id of the user who created this MediaFolder.
     */
    protected function __construct(
        string $name,
        MediaFolder $parent = null,
        int $userId
    ) {
        $this->setName($name);
        $this->parent = $parent;
        $this->userId = $userId;
        $this->items = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @param string $name
     * @param MediaFolder|null $parent
     * @param int $userId
     * @return MediaFolder
     */
    public static function create(
        string $name,
        MediaFolder $parent = null,
        int $userId
    ) : MediaFolder {
        return new self(
            $name,
            $parent,
            $userId
        );
    }

    /**
     * @param string $name
     * @param MediaFolder|null $parent
     */
    public function update(string $name, MediaFolder $parent = null)
    {
        $this->setName($name);

        if ($parent instanceof MediaFolder) {
            $this->setParent($parent);

            return;
        }

        $this->removeParent();
    }

    /**
     * @param MediaFolderDataTransferObject $mediaFolderDataTransferObject
     * @return MediaFolder
     */
    public static function fromDataTransferObject(MediaFolderDataTransferObject $mediaFolderDataTransferObject): MediaFolder
    {
        if ($mediaFolderDataTransferObject->hasExistingMediaFolder()) {
            /** @var MediaFolder $mediaFolder */
            $mediaFolder = $mediaFolderDataTransferObject->getMediaFolderEntity();

            $mediaFolder->update(
                $mediaFolderDataTransferObject->name,
                $mediaFolderDataTransferObject->parent
            );

            return $mediaFolder;
        }

        /** @var MediaFolder $mediaFolder */
        return self::create(
            $mediaFolderDataTransferObject->name,
            $mediaFolderDataTransferObject->parent,
            $mediaFolderDataTransferObject->userId
        );
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'parent' => $this->parent,
            'userId' => $this->userId,
            'name' => $this->name,
            'createdOn' => $this->createdOn->getTimestamp(),
            'editedOn' => $this->editedOn->getTimestamp(),
            'numberOfItems' => $this->getItems()->count(),
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
     * @return MediaFolder|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function hasParent(): bool
    {
        return $this->parent instanceof MediaFolder;
    }

    /**
     * Remove parent
     *
     * @return MediaFolder
     */
    public function removeParent(): self
    {
        $this->parent = null;
        return $this;
    }

    /**
     * @param MediaFolder $parent
     * @return $this
     */
    public function setParent(MediaFolder $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    private function setName(string $name)
    {
        $this->name = Uri::getUrl($name);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn(): \DateTime
    {
        return $this->createdOn;
    }

    /**
     * @return \DateTime
     */
    public function getEditedOn(): \DateTime
    {
        return $this->editedOn;
    }

    /**
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function hasItems(): bool
    {
        return $this->items->count() > 0;
    }

    /**
     * @return bool
     */
    public function hasConnectedItems(): bool
    {
        return self::hasConnectedMediaItems($this->items);
    }

    /**
     * @return Collection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children->count() > 0;
    }

    /**
     * @return bool
     */
    public function hasChildrenWithConnectedItems(): bool
    {
        /** @var MediaFolder $mediaFolder */
        foreach ($this->children as $mediaFolder) {
            if (self::hasFolderChildrenWithConnectedItems($mediaFolder)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param MediaFolder $mediaFolder
     * @return bool
     */
    private static function hasFolderChildrenWithConnectedItems(MediaFolder $mediaFolder): bool
    {
        /** @var MediaItem $mediaItem */
        if (self::hasConnectedMediaItems($mediaFolder->getItems())) {
            return true;
        }

        if ($mediaFolder->hasChildren()) {
            foreach ($mediaFolder->getChildren() as $childFolder) {
                return self::hasFolderChildrenWithConnectedItems($childFolder);
            }
        }

        return false;
    }

    /**
     * @param Collection $mediaItems
     * @return bool
     */
    private static function hasConnectedMediaItems(Collection $mediaItems): bool
    {
        /** @var MediaItem $mediaItem */
        foreach ($mediaItems as $mediaItem) {
            if ($mediaItem->hasGroups()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = $this->editedOn = new \Datetime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->editedOn = new \Datetime();
    }
}
