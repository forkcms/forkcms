<?php

namespace Backend\Modules\ContentBlocks\ContentBlock\Event;

use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;
use Symfony\Component\EventDispatcher\Event;

abstract class ContentBlockEvent extends Event
{
    /** @var ContentBlock */
    private $contentBlock;

    /**
     * @param ContentBlock $contentBlock
     */
    public function __construct(ContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
    }

    /**
     * @return ContentBlock
     */
    public function getContentBlock()
    {
        return $this->contentBlock;
    }
}
