<?php

namespace ForkCMS\Modules\Backend\Domain\User\Command;

final class DeleteUser
{
    public function __construct(private int $userId)
    {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
