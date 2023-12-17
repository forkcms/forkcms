<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;

final class DeleteContentBlock
{
    private ?ContentBlock $contentBlockEntity;

    public function __construct(public readonly int $id)
    {
    }

    public function getEntity(): ContentBlock
    {
        return $this->contentBlockEntity;
    }

    public function setEntity(ContentBlock $contentBlock): void
    {
        $this->contentBlockEntity = $contentBlock;
    }
}
