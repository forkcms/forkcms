<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

abstract class ContentBlockDataTransferObject
{
    protected ?ContentBlock $contentBlockEntity;

    public function __construct(?ContentBlock $contentBlockEntity = null)
    {
        $this->contentBlockEntity = $contentBlockEntity;

        if (!$contentBlockEntity instanceof ContentBlock) {
            return;
        }
    }

    public function hasEntity(): bool
    {
        return null === $this->contentBlockEntity;
    }

    public function getEntity(): ContentBlock
    {
        return $this->contentBlockEntity;
    }
}
