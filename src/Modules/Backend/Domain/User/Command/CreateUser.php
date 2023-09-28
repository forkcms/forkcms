<?php

namespace ForkCMS\Modules\Backend\Domain\User\Command;

use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserDataTransferObject;

final class CreateUser extends UserDataTransferObject
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setEntity(User $userEntity): void
    {
        $this->userEntity = $userEntity;
    }
}
