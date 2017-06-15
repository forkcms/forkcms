<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock;

use Backend\Core\Language\Locale;
use Symfony\Component\Validator\Constraints as Assert;

class ContentBlockDataTransferObject
{
    /**
     * @var ContentBlock
     */
    protected $contentBlockEntity;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $extraId;

    /**
     * @var int|null
     */
    public $revisionId;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $title;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $template = ContentBlock::DEFAULT_TEMPLATE;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $text;

    /**
     * @var bool
     */
    public $isVisible = true;

    /**
     * @var Locale
     */
    public $locale;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var Status
     */
    public $status;

    public function __construct(ContentBlock $contentBlock = null)
    {
        $this->contentBlockEntity = $contentBlock;

        if (!$this->hasExistingContentBlock()) {
            $this->status = Status::active();

            return;
        }

        $this->id = $contentBlock->getId();
        $this->extraId = $contentBlock->getExtraId();
        $this->isVisible = !$contentBlock->isHidden();
        $this->title = $contentBlock->getTitle();
        $this->text = $contentBlock->getText();
        $this->template = $contentBlock->getTemplate();
        $this->userId = $contentBlock->getUserId();
        $this->locale = $contentBlock->getLocale();
        $this->status = $contentBlock->getStatus();
        $this->revisionId = $contentBlock->getRevisionId();
    }

    public function getContentBlockEntity(): ContentBlock
    {
        return $this->contentBlockEntity;
    }

    public function hasExistingContentBlock(): bool
    {
        return $this->contentBlockEntity instanceof ContentBlock;
    }
}
