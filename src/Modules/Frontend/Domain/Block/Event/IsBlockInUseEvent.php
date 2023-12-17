<?php

namespace ForkCMS\Modules\Frontend\Domain\Block\Event;

use ForkCMS\Modules\Frontend\Domain\Block\Block;
use Symfony\Contracts\EventDispatcher\Event;

final class IsBlockInUseEvent extends Event
{
    public function __construct(
        public readonly Block $block,
        private bool $inUse = false,
    ) {
    }

    public function registerUsage(): void
    {
        $this->inUse = true;
        $this->stopPropagation();
    }

    public function isInUse(): bool
    {
        return $this->inUse;
    }
}
