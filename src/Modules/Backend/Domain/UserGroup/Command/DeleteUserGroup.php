<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup\Command;

final class DeleteUserGroup
{
    public function __construct(private int $userGroupId)
    {
    }

    public function getUserGroupId(): int
    {
        return $this->userGroupId;
    }
}
