<?php

namespace Backend\Modules\Pages\Domain\Page;

use Common\Doctrine\Entity\Meta;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Pages\Domain\Page\PageRepository")
 * @ORM\Table(
 *     name="pages",
 *     indexes={
 *      @ORM\Index(name="idx_id_status_hidden_language", columns={"id", "status", "language"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Page
{
    public const ACTIVE = 'active';
    public const ARCHIVE = 'archive';
    public const DRAFT = 'draft';

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
     * @ORM\ManyToOne(targetEntity="Common\Doctrine\Entity\Meta", cascade={"ALL"})
     * @ORM\JoinColumn(name="meta_id", referencedColumnName="id")
     */
    private $meta;

    /**
     * Language of the content
     *
     * @var string
     *
     * @ORM\Column(type="string", length=5, name="language")
     */
    private $language;

    /**
     * page, header, footer, ...
     *
     * @var string
     *
     * @ORM\Column(type="string", name="type")
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
     * @var string
     *
     * @ORM\Column(type="string", length=243)
     */
    private $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="publish_on")
     */
    private $publishOn;

    /**
     * @var mixed|null
     *
     * @ORM\Column(type="text", name="data", nullable=true)
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

    public function __construct(
        int $id,
        int $userId,
        ?int $parentId,
        ?int $templateId,
        Meta $meta,
        string $language,
        string $title,
        string $navigationTitle,
        DateTime $publishOn,
        int $sequence,
        bool $navigationTitleOverwrite = false,
        bool $hidden = true,
        string $status = self::ACTIVE,
        string $type = 'root',
        $data = null,
        bool $allowMove = true,
        bool $allowChildren = true,
        bool $allowEdit = true,
        bool $allowDelete = true
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->parentId = $parentId;
        if ($this->parentId === null) {
            $this->parentId = 0;
        }
        $this->templateId = $templateId;
        if ($this->templateId === null) {
            $this->templateId = 0;
        }
        $this->meta = $meta;
        $this->language = $language;
        $this->title = $title;
        $this->navigationTitle = $navigationTitle;
        $this->publishOn = $publishOn;
        $this->sequence = $sequence;
        $this->type = $type;
        $this->navigationTitleOverwrite = $navigationTitleOverwrite;
        $this->hidden = $hidden;
        $this->status = $status;
        $this->data = serialize($data);
        $this->allowMove = $allowMove;
        $this->allowChildren = $allowChildren;
        $this->allowEdit = $allowEdit;
        $this->allowDelete = $allowDelete;
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

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPublishOn(): DateTime
    {
        return $this->publishOn;
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
        return $this->allowMove;
    }

    public function isAllowChildren(): bool
    {
        return $this->allowChildren;
    }

    public function isAllowEdit(): bool
    {
        return $this->allowEdit;
    }

    public function isAllowDelete(): bool
    {
        return $this->allowDelete;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getData()
    {
        return unserialize($this->data);
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
}
