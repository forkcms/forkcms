<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Common\ModuleExtraType;

/**
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

    public static function fromDataTransferObject(
        MediaGalleryDataTransferObject $mediaGalleryDataTransferObject
    ): MediaGallery {
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

    private function updateFromDataTransferObject(
        MediaGalleryDataTransferObject $mediaGalleryDataTransferObject
    ): MediaGallery {
        $this->title = $mediaGalleryDataTransferObject->title;
        $this->action = $mediaGalleryDataTransferObject->action;
        $this->mediaGroup = $mediaGalleryDataTransferObject->mediaGroup;
        $this->status = $mediaGalleryDataTransferObject->status;
        $this->text = $mediaGalleryDataTransferObject->text;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getModuleExtraId(): int
    {
        return $this->moduleExtraId;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getCreatedOn(): \DateTime
    {
        return $this->createdOn;
    }

    public function getEditedOn(): \DateTime
    {
        return $this->editedOn;
    }

    protected function getExtraLabel(): string
    {
        return '"' . $this->title . '"' . ' - ' . ucfirst($this->action);
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

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
                'edit_url' => Model::createUrlForAction(
                    'MediaGalleryEdit',
                    'MediaGalleries',
                    null,
                    ['id' => $this->id]
                ),
            ]
        );

        // Update hidden
        Model::updateExtra(
            $this->moduleExtraId,
            'hidden',
            false
        );
    }

    public function isVisible(): bool
    {
        return $this->status->isActive();
    }
}
