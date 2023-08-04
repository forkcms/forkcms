<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock;

use Common\Locale;
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

        $this->id = $this->contentBlockEntity->getId();
        $this->extraId = $this->contentBlockEntity->getExtraId();
        $this->isVisible = !$this->contentBlockEntity->isHidden();
        $this->title = $this->contentBlockEntity->getTitle();
        $this->text = $this->contentBlockEntity->getText();
        $this->template = $this->contentBlockEntity->getTemplate();
        $this->userId = $this->contentBlockEntity->getUserId();
        $this->locale = $this->contentBlockEntity->getLocale();
        $this->status = $this->contentBlockEntity->getStatus();
        $this->revisionId = $this->contentBlockEntity->getRevisionId();
    }

    public function forOtherLocale(int $id, int $extraId, Locale $locale): void
    {
        $this->id = $id;
        $this->contentBlockEntity = null;
        $this->revisionId = null;
        $this->extraId = $extraId;
        $this->locale = $locale;
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
