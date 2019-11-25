<?php

namespace Backend\Modules\Pages\Domain\Page;

use Common\Doctrine\Entity\Meta;
use Common\Locale;
use DateTime;

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

    /** @var string */
    public $type;

    /** @var string */
    public $title;

    /** @var string */
    public $navigationTitle;

    /** @var bool */
    public $navigationTitleOverwrite;

    /** @var bool */
    public $hidden;

    /** @var string */
    public $status;

    /** @var DateTime */
    public $publishOn;

    /** @var mixed|null */
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

    public function __construct(Page $page = null)
    {
        if ($page === null) {
            return;
        }

        $this->page = $page;

        $this->id = $page->getId();
        $this->revisionId = $page->getRevisionId();
        $this->userId = $page->getUserId();
        $this->parentId = $page->getParentId();
        $this->templateId = $page->getTemplateId();
        $this->meta = $page->getMeta();
        $this->locale = $page->getLocale();
        $this->type = $page->getType();
        $this->title = $page->getTitle();
        $this->navigationTitle = $page->getNavigationTitle();
        $this->navigationTitleOverwrite = $page->isNavigationTitleOverwrite();
        $this->hidden = $page->isHidden();
        $this->status = $page->getStatus();
        $this->publishOn = $page->getPublishOn();
        $this->data = $page->getData();
        $this->createdOn = $page->getCreatedOn();
        $this->editedOn = $page->getEditedOn();
        $this->allowMove = $page->isAllowMove();
        $this->allowChildren = $page->isAllowChildren();
        $this->allowEdit = $page->isAllowEdit();
        $this->allowDelete = $page->isAllowDelete();
        $this->sequence = $page->getSequence();
    }
}
