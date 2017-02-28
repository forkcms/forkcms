<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MediaFolder
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaFolder
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
     * @var MediaFolder
     *
     * @ORM\ManyToOne(
     *      targetEntity="Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder",
     *      inversedBy="children",
     *      cascade="persist"
     * )
     * @ORM\JoinColumn(
     *      name="parentMediaFolderId",
     *      referencedColumnName="id",
     *      onDelete="cascade"
     * )
     */
    protected $parent;

    /**
     * @var integer
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
     * Construct
     *
     * @param string $name The name of this folder.
     * @param MediaFolder|null $parent The parent of this folder, can be NULL.
     * @param int $userId The id of the user who created this MediaFolder.
     */
    protected function __construct(
        string $name,
        MediaFolder $parent = null,
        int $userId
    ) {
        $this->name = $name;
        if ($parent !== null) {
            $this->parent = $parent;
        }
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
    ) : MediaFolder{
        return new self(
            $name,
            $parent,
            $userId
        );
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
            'parent' => ($this->parent !== null) ? $this->parent->__toArray() : null,
            'userId' => $this->userId,
            'name' => $this->name,
            'createdOn' => $this->createdOn->getTimestamp(),
            'editedOn' => $this->editedOn->getTimestamp(),
        ];
    }

    /**
     * Gets the value of id.
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Has a parent
     *
     * @return mixed
     */
    public function hasParent()
    {
        return ($this->parent);
    }

    /**
     * Gets the value of parent.
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Remove parent
     *
     * @return self
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
     * Gets the value of userId.
     *
     * @return integer
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Set the value of name.
     *
     * @param string $name
     * @return string
     */
    public function setName(string $name): string
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the value of createdOn.
     *
     * @return \DateTime
     */
    public function getCreatedOn(): \DateTime
    {
        return $this->createdOn;
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
     * Gets the value of items.
     *
     * @return ArrayCollection
     */
    public function getItems(): ArrayCollection
    {
        return $this->items;
    }

    /**
     * Gets the value of children.
     *
     * @return ArrayCollection
     */
    public function getChildren(): ArrayCollection
    {
        return $this->children;
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

    /**
     * @param $name
     * @param MediaFolder|null $parent
     */
    public function update(
        string $name,
        MediaFolder $parent = null
    ) {
        $this->setName($name);

        if ($parent instanceof MediaFolder) {
            $this->setParent($parent);
        } else {
            $this->removeParent();
        }
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
        $mediaFolder =  self::create(
            $mediaFolderDataTransferObject->name,
            $mediaFolderDataTransferObject->parent,
            $mediaFolderDataTransferObject->userId
        );
        return $mediaFolder;
    }
}
