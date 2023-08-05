<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup\Command;

use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroupDataTransferObject;

final class ChangeUserGroup extends UserGroupDataTransferObject
{
    public function __construct(UserGroup $userGroup)
    {
        parent::__construct($userGroup);
    }
}
