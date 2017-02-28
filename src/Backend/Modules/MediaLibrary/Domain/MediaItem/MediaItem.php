<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

/**
 * MediaItem
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaItem
{
    // Possible MediaMime types for movies
    const MIME_YOUTUBE = 'youtube';
    const MIME_VIMEO = 'vimeo';

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
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $userId;

    /**
     * @var Type
     *
     * @ORM\Column(type="media_item_type")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
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
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $size;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $width;

    /**
     * @var integer
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
     * @param string $mime
     * @param MediaFolder $folder
     * @param int $userId
     */
    private function __construct(
        string $title,
        string $url,
        Type $type,
        string $mime,
        MediaFolder $folder,
        int $userId
    ) {
        $this->folder = $folder;
        $this->userId = (int) $userId;
        $this->type = $type;
        $this->mime = $mime;
        $this->url = (string) $url;
        $this->title = (string) $title;
        $this->createdOn = new \DateTime();
        $this->editedOn = new \DateTime();
        $this->groups = new ArrayCollection();
    }

    /**
     * @param string $source
     * @param MediaFolder $folder
     * @param int $userId
     * @return MediaItem
     * @throws \Exception
     */
    public static function createFromSource(
        string $source,
        MediaFolder $folder,
        int $userId
    ) : MediaItem {
        try {
            // Define file
            $file = new File($source);

            // We don't have a file
            if (!$file->isFile()) {
                throw new \Exception(
                    'The given source is not a file.'
                );
            }

            /** @var Type $mediaItemType */
            $mediaItemType = Type::fromExtension($file->getExtension());

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
                $file->getMimeType(),
                $folder,
                $userId
            );

            $mediaItem->shardingFolderName = $shardingFolderName;
            $mediaItem->size = $file->getSize();

            // Image
            if ($mediaItemType->isImage()) {
                try {
                    list($width, $height) = getimagesize($source);
                } catch (\Exception $e) {
                    throw new \Exception('Error happened when creating MediaItem from source "' . $source . '". The error = ' . $e->getMessage());
                }

                $mediaItem->setResolution($width, $height);
            }

            return $mediaItem;
        } catch (FileNotFoundException $e) {
            throw new \Exception(
                'This is not a valid file: "' . $source . '".'
            );
        }
    }

    /**
     * @param string $movieService
     * @param string $movieId
     * @param string $movieTitle
     * @param MediaFolder $folder
     * @param int $userId
     * @return MediaItem
     */
    public static function createFromMovieUrl(
        string $movieService,
        string $movieId,
        string $movieTitle,
        MediaFolder $folder,
        int $userId
    ) : MediaItem {
        return new MediaItem(
            $movieId,
            $movieTitle,
            Type::movie(),
            $movieService,
            $folder,
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
            'folder' => $this->folder->__toArray(),
            'userId' => $this->userId,
            'type' => (string) $this->type,
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
     * Gets the value of id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Gets the value of folder.
     *
     * @return MediaFolder
     */
    public function getFolder(): MediaFolder
    {
        return $this->folder;
    }

    /**
     * Sets the value of folder.
     *
     * @param MediaFolder $folder the folder
     * @return self
     */
    public function setFolder(MediaFolder $folder): self
    {
        $this->folder = $folder;
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
     * Gets the value of type.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * Gets the value of mime.
     *
     * @return string
     */
    public function getMime(): string
    {
        return $this->mime;
    }

    /**
     * Get all the mimes for movie
     *
     * @return string[]
     */
    public static function getMimesForMovie(): array
    {
        return [
            self::MIME_YOUTUBE,
            self::MIME_VIMEO,
        ];
    }

    /**
     * Gets the value of shardingFolderName.
     *
     * @return string
     */
    public function getScardingFolderName(): string
    {
        return $this->shardingFolderName;
    }

    /**
     * Gets the value of url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of title.
     *
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = (string) $title;
    }

    /**
     * Gets the value of url.
     *
     * @return string
     */
    public function getFullUrl(): string
    {
        return $this->getScardingFolderName() . '/' . $this->getUrl();
    }

    /**
     * Gets the value of size.
     *
     * @return integer
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set resolution
     *
     * @param int $width
     * @param int $height
     * @return self
     */
    public function setResolution(int $width, int $height): self
    {
        $this->width = (int) $width;
        $this->height = (int) $height;
        return $this;
    }

    /**
     * Gets the value of width.
     *
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Gets the value of height.
     *
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
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
     * Gets the value of groups.
     *
     * @return ArrayCollection
     */
    public function getGroups(): ArrayCollection
    {
        return $this->groups;
    }

    /**
     * @param string|null $subDirectory
     * @return string|null
     */
    public function getAbsolutePath(string $subDirectory = null)
    {
        $subDirectory = self::getSubdirectory($subDirectory);

        return $this->getFullUrl() === null ? null : self::getUploadRootDir($subDirectory) . '/' . $this->getFullUrl();
    }

    /**
     * @param string|null $subDirectory
     * @return string
     */
    protected static function getSubdirectory(string $subDirectory = null): string
    {
        if ($subDirectory === null || $subDirectory === 'source') {
            return 'Source';
        } elseif (strtolower($subDirectory) === 'backend') {
            return 'Backend';
        } elseif (strtolower($subDirectory) === 'frontend') {
            return 'Frontend';
        } else {
            return 'Frontend/' . $subDirectory;
        }
    }

    /**
     * @param string|null $subDirectory
     * @return string
     */
    public function getAbsoluteWebPath(string $subDirectory = null): string
    {
        return SITE_URL . $this->getWebPath($subDirectory);
    }

    /**
     * @param string|null $subDirectory
     * @return string|null
     */
    public function getWebPath(string $subDirectory = null)
    {
        return self::getWebDir($subDirectory) . $this->getFullUrl();
    }

    /**
     * @param string|null $subDirectory
     * @return string
     */
    public static function getWebDir(string $subDirectory = null): string
    {
        $subDirectory = self::getSubdirectory($subDirectory);

        $webPath = FRONTEND_FILES_URL . '/' . self::getTrimmedUploadDir() . '/';
        if ($subDirectory !== null) {
            $webPath .= $subDirectory . '/';
        }

        return $webPath;
    }

    /**
     * @param string|null $subDirectory
     * @return string
     */
    public static function getUploadRootDir(string $subDirectory = null): string
    {
        $subDirectory = self::getSubdirectory($subDirectory);
        $parentUploadRootDir = FRONTEND_FILES_PATH . '/' . self::getTrimmedUploadDir();

        // the absolute directory path where uploaded
        // documents should be saved
        if ($subDirectory !== null) {
            return $parentUploadRootDir . '/' . $subDirectory;
        }

        return $parentUploadRootDir;
    }

    /**
     * @return string
     */
    protected static function getUploadDir(): string
    {
        return 'MediaLibrary';
    }

    /**
     * @return string
     */
    protected static function getTrimmedUploadDir(): string
    {
        return trim(self::getUploadDir(), '/\\');
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
