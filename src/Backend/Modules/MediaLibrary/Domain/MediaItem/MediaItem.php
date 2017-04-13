<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Modules\MediaLibrary\Component\StorageProvider\LiipImagineBundleStorageProviderInterface;
use Backend\Modules\MediaLibrary\Component\StorageProvider\StorageProviderInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Core\Engine\Model;

/**
 * MediaItem
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaItem
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
     * @var MediaFolder
     *
     * @ORM\ManyToOne(
     *      targetEntity="Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder",
     *      inversedBy="items",
     *      cascade="persist"
     * )
     * @ORM\JoinColumn(
     *      name="mediaFolderId",
     *      referencedColumnName="id",
     *      onDelete="cascade"
     * )
     */
    protected $folder;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $userId;

    /**
     * @var StorageType
     *
     * @ORM\Column(type="media_item_storage_type", options={"default"="local"})
     */
    protected $storageType;

    /**
     * @var Type
     *
     * @ORM\Column(type="media_item_type")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $mime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $shardingFolderName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $size;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $width;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $height;

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
     *     targetEntity="Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem",
     *     mappedBy="item",
     *     cascade={"persist","merge"},
     *     orphanRemoval=true
     * )
     */
    protected $groups;

    /**
     * MediaItem constructor.
     *
     * @param string $title
     * @param string $url
     * @param Type $type
     * @param StorageType $storageType
     * @param MediaFolder $folder
     * @param int $userId
     */
    private function __construct(
        string $title,
        string $url,
        Type $type,
        StorageType $storageType,
        MediaFolder $folder,
        int $userId
    ) {
        $this->folder = $folder;
        $this->userId = $userId;
        $this->type = $type;
        $this->storageType = $storageType;
        $this->url = $url;
        $this->title = $title;
        $this->createdOn = new \DateTime();
        $this->editedOn = new \DateTime();
        $this->groups = new ArrayCollection();
    }

    /**
     * @param string $path
     * @param MediaFolder $folder
     * @param int $userId
     * @return MediaItem
     * @throws \Exception
     */
    public static function createFromLocalStorageType(
        string $path,
        MediaFolder $folder,
        int $userId
    ) : MediaItem {
        try {
            // Define file from path
            $file = new File($path);

            // We don't have a file
            if (!$file->isFile()) {
                throw new \Exception(
                    'The given source is not a file.'
                );
            }

            /** @var Type $mediaItemType */
            $mediaItemType = Type::fromMimeType($file->getMimeType());

            // Define sharding folder (getPath gets the path without the trailing slash)
            /** @var string $shardingFolderName */
            $shardingFolderName = basename($file->getPath());

            // Define title
            /** @var string $shardingFolderName */
            $title = str_replace('.' . $file->getExtension(), '', $file->getFilename());

            /** @var MediaItem $mediaItem */
            $mediaItem = new self(
                $title,
                $file->getFilename(),
                $mediaItemType,
                StorageType::local(),
                $folder,
                $userId
            );

            $mediaItem->mime = $file->getMimeType();
            $mediaItem->shardingFolderName = $shardingFolderName;
            $mediaItem->size = $file->getSize();

            // Image
            if ($mediaItemType->isImage()) {
                try {
                    list($width, $height) = getimagesize($path);
                } catch (\Exception $e) {
                    throw new \Exception('Error happened when creating MediaItem from path "' . $path . '". The error = ' . $e->getMessage());
                }

                $mediaItem->setResolution($width, $height);
            }

            return $mediaItem;
        } catch (FileNotFoundException $e) {
            throw new \Exception(
                'This is not a valid file: "' . $path . '".'
            );
        }
    }

    /**
     * @param StorageType $movieStorageType
     * @param string $movieId
     * @param string $movieTitle
     * @param MediaFolder $folder
     * @param int $userId
     * @return MediaItem
     */
    public static function createFromMovieUrl(
        StorageType $movieStorageType,
        string $movieId,
        string $movieTitle,
        MediaFolder $folder,
        int $userId
    ) : MediaItem {
        return new MediaItem(
            $movieTitle,
            $movieId,
            Type::movie(),
            $movieStorageType,
            $folder,
            $userId
        );
    }

    /**
     * @param MediaItemDataTransferObject $mediaItemDataTransferObject
     * @return MediaItem
     */
    public static function fromDataTransferObject(MediaItemDataTransferObject $mediaItemDataTransferObject)
    {
        if ($mediaItemDataTransferObject->hasExistingMediaItem()) {
            $mediaItem = $mediaItemDataTransferObject->getMediaItemEntity();

            $mediaItem->title = $mediaItemDataTransferObject->title;
            $mediaItem->folder = $mediaItemDataTransferObject->folder;
            $mediaItem->userId = $mediaItemDataTransferObject->userId;
            $mediaItem->url = $mediaItemDataTransferObject->url;

            return $mediaItem;
        }
    }

    /**
     * @return array
     */
    public function __toArray(): array
    {
        return [
            'id' => $this->id,
            'folder' => $this->folder->__toArray(),
            'userId' => $this->userId,
            'type' => (string) $this->type,
            'storageType' => (string) $this->storageType,
            'mime' => $this->mime,
            'shardingFolderName' => $this->shardingFolderName,
            'url' => $this->url,
            'fullUrl' => $this->getFullUrl(),
            'title' => $this->title,
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'createdOn' => $this->createdOn->getTimestamp(),
            'editedOn' => $this->editedOn->getTimestamp(),
            'source' => $this->getWebPath(),
            'preview_source' => $this->getWebPath('backend'),
            $this->type->getType() => true,
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return MediaFolder
     */
    public function getFolder(): MediaFolder
    {
        return $this->folder;
    }

    /**
     * @param MediaFolder $folder the folder
     * @return MediaItem
     */
    public function setFolder(MediaFolder $folder): self
    {
        $this->folder = $folder;
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
     * @return StorageType
     */
    public function getStorageType(): StorageType
    {
        return $this->storageType;
    }

    /**
     * @param StorageType $storageType
     */
    public function setStorageType(StorageType $storageType)
    {
        $this->storageType = $storageType;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMime(): string
    {
        return $this->mime;
    }

    /**
     * @return string|null
     */
    public function getScardingFolderName()
    {
        return $this->shardingFolderName;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFullUrl(): string
    {
        return $this->getScardingFolderName() . '/' . $this->getUrl();
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $width
     * @param int $height
     * @return MediaItem
     */
    public function setResolution(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
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
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @return bool
     */
    public function hasGroups(): bool
    {
        return $this->groups->count() > 0;
    }

    /**
     * @return string|null
     */
    public function getAbsolutePath(): string
    {
        return Model::get('media_library.manager.storage')->getStorageProvider($this->getStorageType())->getAbsolutePath($this);
    }

    /**
     * @return string
     */
    public function getAbsoluteWebPath(): string
    {
        return Model::get('media_library.manager.storage')->getStorageProvider($this->getStorageType())->getAbsoluteWebPath($this);
    }

    /**
     * @return string|null
     */
    public function getLinkHTML(): string
    {
        return Model::get('media_library.manager.storage')->getStorageProvider($this->getStorageType())->getLinkHTML($this);
    }

    /**
     * @return string|null
     */
    public function getIncludeHTML(): string
    {
        return Model::get('media_library.manager.storage')->getStorageProvider($this->getStorageType())->getIncludeHTML($this);
    }

    /**
     * @param string|null $filter The LiipImagineBundle filter name you want to use.
     * @return string|null
     */
    public function getWebPath(string $filter = null): string
    {
        /** @var StorageProviderInterface $storage */
        $storage = Model::get('media_library.manager.storage')->getStorageProvider($this->getStorageType());

        if (!$storage instanceof LiipImagineBundleStorageProviderInterface || $filter === null) {
            return $storage->getWebPath($this);
        }

        return $storage->getWebPathWithFilter($this, $filter);
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
