<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroupRepository;
use ForkCMS\Modules\Backend\Domain\UserGroup\Event\UserGroupDeletedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteUserGroupHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserGroupRepository $userGroupRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(DeleteUserGroup $deleteUserGroup): void
    {
        $userGroup = $this->userGroupRepository->find($deleteUserGroup->getUserGroupId());
        $this->userGroupRepository->remove($userGroup);
        $this->eventDispatcher->dispatch(new UserGroupDeletedEvent($userGroup));
    }
}
