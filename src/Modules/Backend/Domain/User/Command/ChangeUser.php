<?php

namespace ForkCMS\Modules\Backend\Domain\User\Command;

use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserDataTransferObject;

final class ChangeUser extends UserDataTransferObject
{
    public function __construct(User $user, ?string $qrCode = null)
    {
        parent::__construct($user);
    }
}
