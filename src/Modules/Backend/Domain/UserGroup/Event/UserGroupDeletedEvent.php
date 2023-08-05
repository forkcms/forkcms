<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup\Event;

use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use Symfony\Contracts\EventDispatcher\Event;

final class UserGroupDeletedEvent extends Event
{
    public function __construct(public readonly UserGroup $userGroup)
    {
    }
}
