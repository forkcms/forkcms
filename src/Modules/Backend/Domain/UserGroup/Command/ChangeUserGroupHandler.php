<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Backend\Domain\UserGroup\Event\UserGroupChangedEvent;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroupRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChangeUserGroupHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserGroupRepository $userGroupRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ChangeUserGroup $changeUserGroup): void
    {
        $this->userGroupRepository->save(UserGroup::fromDataTransferObject($changeUserGroup));
        $this->eventDispatcher->dispatch(new UserGroupChangedEvent($changeUserGroup->getEntity()));
    }
}
