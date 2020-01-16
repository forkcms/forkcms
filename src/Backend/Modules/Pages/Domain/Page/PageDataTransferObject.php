<?php

namespace Backend\Modules\Pages\Domain\Page;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type as MediaGroupType;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockDataTransferObject;
use Backend\Modules\Tags\Engine\Model;
use Common\Doctrine\Entity\Meta;
use Common\Locale;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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

    /** @var MediaGroup */
    public $image;

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

    /** @var array */
    public $blocks;

    public function __construct(Page $page = null, int $templateId = null)
    {
        $this->page = $page;
        $this->blocks = [];

        if (!$this->hasExistingPage()) {
            $this->allowChildren = true;
            $this->allowDelete = true;
            $this->allowEdit = true;
            $this->allowMove = true;
            $this->navigationTitleOverwrite = false;
            $this->hidden = false;
            $this->image = MediaGroup::create(MediaGroupType::image());
            $this->publishOn = new DateTime();
            $this->templateId = $templateId;

            return;
        }

        $this->id = $this->page->getId();
        $this->userId = $this->page->getUserId();
        $this->parentId = $this->page->getParentId();
        // update the template id if it isn't null
        $this->templateId = $templateId === 0 || $templateId === null ? $this->page->getTemplateId() : $templateId;
        $this->meta = clone $this->page->getMeta();
        $this->locale = $this->page->getLocale();
        $this->type = $this->page->getType();
        $this->title = $this->page->getTitle();
        $this->navigationTitle = $this->page->getNavigationTitle();
        $this->navigationTitleOverwrite = $this->page->isNavigationTitleOverwrite();
        $this->hidden = $this->page->isHidden();
        $this->status = $this->page->getStatus();
        $this->publishOn = $this->hidden ? new DateTime() : $this->page->getPublishOn();
        $this->publishUntil = $this->hidden ? null : $this->page->getPublishUntil();
        $this->data = $this->page->getData();
        $this->image = $this->page->getImage() ?? MediaGroup::create(MediaGroupType::image());
        $this->createdOn = $this->page->getCreatedOn();
        $this->editedOn = $this->page->getEditedOn();
        $this->allowMove = $this->page->isAllowMove();
        $this->allowChildren = $this->page->isAllowChildren();
        $this->allowEdit = $this->page->isAllowEdit();
        $this->allowDelete = $this->page->isAllowDelete();
        $this->sequence = $this->page->getSequence();
        $this->tags = Model::getTags('Pages', $this->id);

        foreach ($this->page->getBlocks() as $block) {
            $position = $block->getPosition();
            $this->blocks[$position] = $this->blocks[$position] ?? new ArrayCollection();
            $this->blocks[$position]->add(new PageBlockDataTransferObject($block));
        }
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
