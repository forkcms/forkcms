<?php

namespace ForkCMS\Modules\Backend\Domain\User\Command;

use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserDataTransferObject;

class DisableTwoFactorAuthentication
{
    public function __construct(public readonly User $user)
    {
    }
}
