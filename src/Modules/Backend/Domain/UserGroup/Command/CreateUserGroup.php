<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup\Command;

use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroupDataTransferObject;

final class CreateUserGroup extends UserGroupDataTransferObject
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setEntity(UserGroup $userGroupEntity): void
    {
        $this->userGroupEntity = $userGroupEntity;
    }
}
