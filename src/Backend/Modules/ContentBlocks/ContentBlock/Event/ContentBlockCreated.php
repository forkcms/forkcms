<?php

namespace Backend\Modules\ContentBlocks\ContentBlock\Event;

use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;
use Symfony\Component\EventDispatcher\Event;

class ContentBlockCreated extends Event
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'content_blocks.event.content_block_created';

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
