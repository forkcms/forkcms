<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Event;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\DeleteContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Symfony\Contracts\EventDispatcher\Event;

final class ContentBlockDeletedEvent extends Event
{
    public function __construct(public readonly ContentBlock $contentBlock)
    {
    }

    public static function fromDeleteCommand(DeleteContentBlock $deleteContentBlock): self
    {
        return new self($deleteContentBlock->getEntity());
    }
}
