<?php

namespace ForkCMS\Modules\Frontend\Domain\Block\Event;

use ForkCMS\Modules\Frontend\Domain\Block\Block;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeDeleteBlockEvent extends Event
{
    public function __construct(public readonly Block $block)
    {
    }
}
