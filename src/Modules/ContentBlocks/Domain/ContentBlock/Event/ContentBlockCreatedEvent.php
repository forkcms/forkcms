<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Event;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\CreateContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Symfony\Contracts\EventDispatcher\Event;

final class ContentBlockCreatedEvent extends Event
{
    public function __construct(public readonly ContentBlock $contentBlock)
    {
    }

    public static function fromCreateCommand(CreateContentBlock $createContentBlock): self
    {
        return new self($createContentBlock->getEntity());
    }
}
