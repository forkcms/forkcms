<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Symfony\Component\Validator\Constraints as Assert;

abstract class ContentBlockCommand
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $template = ContentBlock::DEFAULT_TEMPLATE;

    /**
     * @var string
     *
     * @Assert\NotBlank()
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
}
