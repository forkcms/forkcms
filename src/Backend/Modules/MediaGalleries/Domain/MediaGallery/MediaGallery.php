<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Common\ModuleExtraType;

/**
 * MediaGallery
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaGallery
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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $userId;

    /**
     * @var int
     *
     * ToDo: when Fork CMS has ModuleExtra entity in core, use its Entity as type
     *
     * @ORM\Column(type="integer")
     */
    protected $moduleExtraId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $action;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $text;

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
     * @var Status
     *
     * @ORM\Column(type="media_gallery_status")
     */
    protected $status;

    /**
     * @var MediaGroup
     *
     * @ORM\OneToOne(
     *      targetEntity="Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup",
     *      cascade="persist",
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(
     *      name="mediaGroupId",
     *      referencedColumnName="id",
     *      onDelete="cascade"
     * )
     */
    protected $mediaGroup;

    /**
     * MediaGallery constructor.
     *
     * @param string $title
     * @param string $action
     * @param int $userId
     * @param MediaGroup $mediaGroup
     * @param Status $status
     * @param string|null $text
     */
    private function __construct(
        string $title,
        string $action,
        int $userId,
        MediaGroup $mediaGroup,
        Status $status,
        string $text = null
    ) {
        $this->userId = $userId;
        $this->action = $action;
        $this->title = $title;
        $this->mediaGroup = $mediaGroup;
        $this->status = $status;
        $this->text = $text;
    }

    /**
     * @param MediaGalleryDataTransferObject $mediaGalleryDataTransferObject
     * @return MediaGallery
     */
    public static function fromDataTransferObject(MediaGalleryDataTransferObject $mediaGalleryDataTransferObject): MediaGallery
    {
        if ($mediaGalleryDataTransferObject->hasExistingMediaGallery()) {
            return $mediaGalleryDataTransferObject
                ->getMediaGalleryEntity()
                ->updateFromDataTransferObject($mediaGalleryDataTransferObject);
        }

        return new self(
            $mediaGalleryDataTransferObject->title,
            $mediaGalleryDataTransferObject->action,
            $mediaGalleryDataTransferObject->userId,
            $mediaGalleryDataTransferObject->mediaGroup,
            $mediaGalleryDataTransferObject->status,
            $mediaGalleryDataTransferObject->text
        );
    }

    /**
     * @param MediaGalleryDataTransferObject $mediaGalleryDataTransferObject
     * @return MediaGallery
     */
    private function updateFromDataTransferObject(
        MediaGalleryDataTransferObject $mediaGalleryDataTransferObject
    ) : MediaGallery {
        $this->title = $mediaGalleryDataTransferObject->title;
        $this->action = $mediaGalleryDataTransferObject->action;
        $this->mediaGroup = $mediaGalleryDataTransferObject->mediaGroup;
        $this->status = $mediaGalleryDataTransferObject->status;
        $this->text = $mediaGalleryDataTransferObject->text;

        return $this;
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
     * Gets the value of userId.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Gets the value of moduleExtraId.
     *
     * @return int
     */
    public function getModuleExtraId(): int
    {
        return $this->moduleExtraId;
    }

    /**
     * Gets the Action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
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
     * Gets the value of text.
     *
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
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
     * Get extra label
     *
     * @return string The gallery extra_label.
     */
    protected function getExtraLabel(): string
    {
        return '"' . $this->title . '"' . ' - ' . ucfirst($this->action);
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * Gets the value of mediaGroup.
     *
     * @return MediaGroup
     */
    public function getMediaGroup(): MediaGroup
    {
        return $this->mediaGroup;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = $this->editedOn = new \Datetime();

        $this->moduleExtraId = Model::insertExtra(
            ModuleExtraType::widget(),
            'MediaGalleries',
            'Gallery',
            'Gallery',
            [],
            false
        );
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->editedOn = new \Datetime();
    }

    /**
     * @ORM\PostRemove
     */
    public function onPostRemove()
    {
        Model::deleteExtraById(
            $this->moduleExtraId,
            true
        );
    }

    /**
     * Update module extra data
     *
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function updateModuleExtraData()
    {
        // Update ModuleExtra data
        Model::updateExtra(
            $this->getModuleExtraId(),
            'data',
            [
                'gallery_id' => $this->id,
                'extra_label' => $this->getExtraLabel(),
                'edit_url' =>
                    Model::createURLForAction('MediaGalleryEdit', 'MediaGalleries')
                    . '&id=' . $this->id,
            ]
        );

        // Update hidden
        Model::updateExtra(
            $this->moduleExtraId,
            'hidden',
            'N'
        );
    }

    /**
     * Is visible only returns true if the "status" is "active"
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->status->isActive();
    }
}
