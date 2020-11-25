<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\Page\Page;
use Common\Locale;
use Symfony\Component\Validator\Constraints as Assert;

class PageBlockDataTransferObject
{
    /**
     * @var PageBlock
     */
    private $pageBlockEntity;

    /**
     * @var Page
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $page;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $position;

    /**
     * @var int|null
     */
    public $extraId;

    /**
     * @var Type
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $extraType;

    /**
     * @var string|null
     */
    public $extraData;

    /**
     * @var string|null
     */
    public $html;

    /**
     * @var bool
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $visible;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $sequence;

    /**
     * @var string
     */
    public $extraModule;

    /**
     * @var string
     */
    public $extraLabel;

    public function __construct(PageBlock $pageBlock = null)
    {
        $this->pageBlockEntity = $pageBlock;

        if (!$this->hasExistingPageBlock()) {
            $this->visible = true;
            $this->extraType = Type::richText();

            return;
        }

        $this->page = $this->pageBlockEntity->getPage();
        $this->position = $this->pageBlockEntity->getPosition();
        $this->extraId = $this->pageBlockEntity->getExtraId();
        $this->extraType = $this->pageBlockEntity->getExtraType();
        $this->extraData = $this->pageBlockEntity->getExtraData();
        $this->html = $this->pageBlockEntity->getHtml();
        $this->visible = $this->pageBlockEntity->isVisible();
        $this->sequence = $this->pageBlockEntity->getSequence();
    }

    public function getPageBlockEntity(): PageBlock
    {
        return $this->pageBlockEntity;
    }

    public function hasExistingPageBlock(): bool
    {
        return $this->pageBlockEntity instanceof PageBlock;
    }

    public function setModuleExtra(?ModuleExtra $moduleExtra): void
    {
        if ($moduleExtra === null) {
            $this->extraId = null;
            $this->extraType = Type::richText();

            return;
        }

        $this->extraType = $moduleExtra->getType()->getPageBlockType();
        $this->extraId = $moduleExtra->getId();
    }
}
