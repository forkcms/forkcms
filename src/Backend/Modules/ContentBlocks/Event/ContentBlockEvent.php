<?php

namespace Backend\Modules\ContentBlocks\Event;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Symfony\Component\EventDispatcher\Event;

abstract class ContentBlockEvent extends Event
{
    /** @var ContentBlock */
    private $contentBlock;

    public function __construct(ContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
    }

    public function getContentBlock(): ContentBlock
    {
        return $this->contentBlock;
    }
}
