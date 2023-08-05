<?php

namespace ForkCMS\Modules\Backend\Domain\User\Event;

use ForkCMS\Modules\Backend\Domain\User\User;
use Symfony\Contracts\EventDispatcher\Event;

final class UserDeletedEvent extends Event
{
    public function __construct(public readonly User $user)
    {
    }
}
