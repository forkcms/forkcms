<?php

namespace Backend\Modules\Pages\Domain\Page;

use Backend\Modules\Tags\Engine\Model;
use Common\Doctrine\Entity\Meta;
use Common\Locale;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class PageDataTransferObject
{
    /** @var Page|null */
    public $page;

    /** @var int */
    public $id;

    /** @var int */
    public $revisionId;

    /** @var int */
    public $userId;

    /** @var int */
    public $parentId;

    /** @var int */
    public $templateId;

    /** @var Meta */
    public $meta;

    /** @var Locale */
    public $locale;

    /** @var Type */
    public $type;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $title;

    /**
     * @var string|null
     *
     * @Assert\Expression("this.hasValidNavigationTitle()", message="err.FieldIsRequired")
     */
    public $navigationTitle;

    /**
     * @var bool
     */
    public $navigationTitleOverwrite;

    /** @var bool */
    public $hidden;

    /** @var string */
    public $status;

    /** @var DateTime */
    public $publishOn;

    /** @var DateTime|null */
    public $publishUntil;

    /** @var array|null */
    public $data;

    /** @var DateTime */
    public $createdOn;

    /** @var DateTime */
    public $editedOn;

    /** @var bool */
    public $allowMove;

    /** @var bool */
    public $allowChildren;

    /** @var bool */
    public $allowEdit;

    /** @var bool */
    public $allowDelete;

    /** @var int */
    public $sequence;

    /** @var string */
    public $tags;

    public function __construct(Page $page = null)
    {
        $this->page = $page;

        if (!$this->hasExistingPage()) {
            $this->allowChildren = true;
            $this->allowDelete = true;
            $this->allowEdit = true;
            $this->allowMove = true;
            $this->navigationTitleOverwrite = false;
            $this->hidden = false;

            return;
        }

        $this->id = $this->page->getId();
        $this->revisionId = $this->page->getRevisionId();
        $this->userId = $this->page->getUserId();
        $this->parentId = $this->page->getParentId();
        $this->templateId = $this->page->getTemplateId();
        $this->meta = $this->page->getMeta();
        $this->locale = $this->page->getLocale();
        $this->type = $this->page->getType();
        $this->title = $this->page->getTitle();
        $this->navigationTitle = $this->page->getNavigationTitle();
        $this->navigationTitleOverwrite = $this->page->isNavigationTitleOverwrite();
        $this->hidden = $this->page->isHidden();
        $this->status = $this->page->getStatus();
        $this->publishOn = $this->page->getPublishOn();
        $this->publishUntil = $this->page->getPublishUntil();
        $this->data = $this->page->getData();
        $this->createdOn = $this->page->getCreatedOn();
        $this->editedOn = $this->page->getEditedOn();
        $this->allowMove = $this->page->isAllowMove();
        $this->allowChildren = $this->page->isAllowChildren();
        $this->allowEdit = $this->page->isAllowEdit();
        $this->allowDelete = $this->page->isAllowDelete();
        $this->sequence = $this->page->getSequence();
        $this->tags = Model::getTags('Pages', $this->id);
    }

    public function getPageEntity(): Page
    {
        return $this->page;
    }

    public function hasExistingPage(): bool
    {
        return $this->page instanceof Page;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getId(): ?int
    {
        if ($this->hasExistingPage()) {
            return $this->page->getId();
        }

        return null;
    }

    public function isAction(): bool
    {
        return $this->data['is_action'] ?? false;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function hasValidNavigationTitle(): bool
    {
        return !$this->navigationTitleOverwrite
               || ($this->navigationTitleOverwrite && $this->navigationTitle !== null && $this->navigationTitle !== '');
    }
}
