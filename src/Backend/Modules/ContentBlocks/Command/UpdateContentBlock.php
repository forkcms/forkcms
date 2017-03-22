<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateContentBlock
{
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
     * @var ContentBlock
     */
    public $contentBlock;

    /**
     * @var int
     */
    public $userId;

    /**
     * @param ContentBlock $contentBlock
     */
    public function __construct(ContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;

        $this->isVisible = !$contentBlock->isHidden();
        $this->title = $contentBlock->getTitle();
        $this->text = $contentBlock->getText();
        $this->template = $contentBlock->getTemplate();
        $this->userId = $contentBlock->getUserId();
    }
}
