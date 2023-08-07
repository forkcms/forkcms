<?php

namespace ForkCMS\Modules\Pages\Domain\Revision\Event;

use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use Symfony\Contracts\EventDispatcher\Event;

final class RevisionCreatedEvent extends Event
{
    public function __construct(public readonly Revision $revision)
    {
    }
}
