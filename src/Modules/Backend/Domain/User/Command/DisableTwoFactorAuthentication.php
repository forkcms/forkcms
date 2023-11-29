<?php

namespace ForkCMS\Modules\Backend\Domain\User\Command;

use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserDataTransferObject;

class DisableTwoFactorAuthentication extends UserDataTransferObject
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
