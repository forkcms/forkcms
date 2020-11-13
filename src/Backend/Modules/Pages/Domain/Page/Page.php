<?php

namespace Backend\Modules\Pages\Domain\Page;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\Pages\Domain\PageBlock\PageBlock;
use Common\Doctrine\Entity\Meta;
use Common\Locale;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Frontend\Core\Engine\Navigation;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Pages\Domain\Page\PageRepository")
 * @ORM\Table(
 *     name="PagesPage",
 *     indexes={
 *      @ORM\Index(name="idx_id_status_hidden_locale", columns={"id", "status", "locale"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Page
{
    public const NO_PARENT_PAGE_ID = 0;
    public const HOME_PAGE_ID = 1;
    public const ERROR_PAGE_ID = 404;
    public const TOP_LEVEL_IDS = [
        self::NO_PARENT_PAGE_ID,
        self::HOME_PAGE_ID,
        2,
        3,
        4,
    ];

    /**
     * The real page id although revision id is the real one... (legacy stuff here)
     * @todo Fix this legacy stuff with the multiple ids.
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="id")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="revision_id")
     */
    private $revisionId;

    /**
     * Which user created this page
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="user_id")
     */
    private $userId;

    /**
     * The parent if for the page
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="parent_id")
     */
    private $parentId;

    /**
     * @TODO switch this over to the template entity
     *
     * The template to use
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="template_id")
     */
    private $templateId;

    /**
     * Linked meta information
     *
     * @var Meta
     *
     * @ORM\OneToOne(targetEntity="Common\Doctrine\Entity\Meta", cascade={"ALL"})
     * @ORM\JoinColumn(name="meta_id", referencedColumnName="id", nullable=false)
     */
    private Meta $meta;

    /**
     * @var MediaGroup|null
     *
     * @ORM\ManyToOne(targetEntity="Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup", cascade={"ALL"})
     */
    private $image;

    /**
     * @var Locale
     *
     * @ORM\Column(type="locale")
     */
    private $locale;

    /**
     * @var Type
     *
     * @ORM\Column(type="pages_page_type")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="title")
     */
    private $title;

    /**
     *
     * Title that will be used in the navigation
     *
     * @var string
     *
     * @ORM\Column(type="string", name="navigation_title")
     */
    private $navigationTitle;

    /**
     * Should we override the navigation title
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", name="navigation_title_overwrite")
     */
    private $navigationTitleOverwrite;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="hidden")
     */
    private $hidden;

    /**
     * Is this the active, archive or draft version
     *
     * @var Status
     *
     * @ORM\Column(type="page_status", length=243)
     */
    private $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="publish_on")
     */
    private $publishOn;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", name="publish_until", nullable=true)
     */
    private $publishUntil;

    /**
     * @var array
     */
    private $unserialisedData;

    /**
     * @var string|null
     *
     * Only can be string during persisting or updating in the database as it then contains the serialised value
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="created_on")
     */
    private $createdOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="edited_on")
     */
    private $editedOn;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="allow_move")
     */
    private $allowMove;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="allow_children")
     */
    private $allowChildren;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="allow_edit")
     */
    private $allowEdit;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="allow_delete")
     */
    private $allowDelete;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="sequence")
     */
    private $sequence;

    /**
     * @var PageBlock[]|Collection
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Pages\Domain\PageBlock\PageBlock",
     *     mappedBy="page",
     *     cascade={"remove"}
     * )
     * @ORM\OrderBy({"sequence" = "ASC"})
     */
    private $blocks;

    public function __construct(
        int $id,
        int $userId,
        ?int $parentId,
        ?int $templateId,
        Meta $meta,
        Locale $locale,
        string $title,
        string $navigationTitle,
        ?MediaGroup $image,
        DateTime $publishOn,
        ?DateTime $publishUntil,
        int $sequence,
        bool $navigationTitleOverwrite = false,
        bool $hidden = true,
        Status $status = null,
        Type $type = null,
        array $unserialisedData = null,
        bool $allowMove = true,
        bool $allowChildren = true,
        bool $allowEdit = true,
        bool $allowDelete = true
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->allowMove = !self::isForbiddenToMove($this->id) && $allowMove;
        $this->allowChildren = !self::isForbiddenToHaveChildren($this->id) && $allowChildren;
        $this->allowDelete = !self::isForbiddenToDelete($this->id) && $allowDelete;
        $this->allowEdit = $allowEdit;
        $this->parentId = $parentId;
        if ($this->parentId === null) {
            $this->parentId = 0;
        }
        $this->templateId = $templateId;
        if ($this->templateId === null) {
            $this->templateId = 0;
        }
        $this->meta = $meta;
        $this->locale = $locale;
        $this->title = $title;
        $this->navigationTitle = $navigationTitle;
        $this->image = $image;
        $this->publishOn = $publishOn;
        $this->publishUntil = $publishUntil;
        $this->sequence = $sequence;
        $this->type = $type ?? Type::root();
        $this->navigationTitleOverwrite = $navigationTitleOverwrite;
        $this->hidden = $hidden;
        $this->status = $status;
        if ($this->status === null) {
            $this->status = Status::active();
        }
        $this->unserialisedData = $unserialisedData;
        $this->blocks = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRevisionId(): int
    {
        return $this->revisionId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getImage(): ?MediaGroup
    {
        return $this->image;
    }

    public function getImageItem(): ?MediaItem
    {
        if ($this->image === null) {
            return null;
        }

        return $this->image->getFirstConnectedMediaItem();
    }

    public function getNavigationTitle(): string
    {
        return $this->navigationTitle;
    }

    public function isNavigationTitleOverwrite(): bool
    {
        return $this->navigationTitleOverwrite;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getPublishOn(): DateTime
    {
        return $this->publishOn;
    }

    public function getPublishUntil(): ?DateTime
    {
        return $this->publishUntil;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function getEditedOn(): DateTime
    {
        return $this->editedOn;
    }

    public function isAllowMove(): bool
    {
        return $this->allowMove && !self::isForbiddenToMove($this->id);
    }

    public function isAllowChildren(): bool
    {
        return $this->allowChildren && !self::isForbiddenToHaveChildren($this->id);
    }

    public function isAllowEdit(): bool
    {
        return $this->allowEdit;
    }

    public function isAllowDelete(): bool
    {
        return $this->allowDelete && !self::isForbiddenToDelete($this->id);
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getData(): ?array
    {
        return $this->unserialisedData;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedOn(): void
    {
        $this->createdOn = new DateTime();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setEditedOn(): void
    {
        $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function serialiseData(): void
    {
        if (!empty($this->unserialisedData)) {
            $this->data = serialize($this->unserialisedData);

            return;
        }
        $this->data = null;
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @ORM\PostLoad
     */
    public function unserialiseData(): void
    {
        if ($this->data === null) {
            $this->unserialisedData = [];
            return;
        }
        $this->unserialisedData = unserialize($this->data, ['allowed_classes' => false]);
    }

    public function archive(): void
    {
        $this->status = Status::archive();
    }

    /**
     * @todo This should become an instance method
     */
    public static function isForbiddenToDelete(int $pageId): bool
    {
        return in_array($pageId, [self::HOME_PAGE_ID, self::ERROR_PAGE_ID], true);
    }

    /**
     * @todo This should become an instance method
     */
    public static function isForbiddenToMove(int $pageId): bool
    {
        return in_array($pageId, [self::HOME_PAGE_ID, self::ERROR_PAGE_ID], true);
    }

    /**
     * @todo This should become an instance method
     */
    public static function isForbiddenToHaveChildren(int $pageId): bool
    {
        return $pageId === self::ERROR_PAGE_ID;
    }

    public function move(int $parentId, int $sequence, Type $type): void
    {
        $this->parentId = $parentId;
        $this->sequence = $sequence;
        $this->type = $type;
    }

    public static function fromDataTransferObject(PageDataTransferObject $pageDataTransferObject): self
    {
        return new self(
            $pageDataTransferObject->id,
            $pageDataTransferObject->userId,
            $pageDataTransferObject->parentId,
            $pageDataTransferObject->templateId,
            $pageDataTransferObject->meta,
            $pageDataTransferObject->locale,
            $pageDataTransferObject->title,
            $pageDataTransferObject->navigationTitle ?? $pageDataTransferObject->title,
            $pageDataTransferObject->image,
            $pageDataTransferObject->publishOn,
            $pageDataTransferObject->publishUntil,
            $pageDataTransferObject->sequence,
            $pageDataTransferObject->navigationTitleOverwrite,
            $pageDataTransferObject->hidden,
            $pageDataTransferObject->status,
            $pageDataTransferObject->type,
            $pageDataTransferObject->data,
            $pageDataTransferObject->allowMove,
            $pageDataTransferObject->allowChildren,
            $pageDataTransferObject->allowEdit,
            $pageDataTransferObject->allowDelete
        );
    }

    /**
     * @return PageBlock[]|Collection
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function getUrl(): string
    {
        return Navigation::getUrl($this->id, $this->locale->getLocale());
    }
}
