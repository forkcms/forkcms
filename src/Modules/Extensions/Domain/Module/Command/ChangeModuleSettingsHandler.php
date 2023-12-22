<?php

namespace ForkCMS\Modules\Extensions\Domain\Module\Command;

use ForkCMS\Core\Domain\Kernel\Event\ClearCacheEvent;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Backend\Domain\User\UserRepository;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChangeModuleSettingsHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ModuleRepository $moduleRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function __invoke(ChangeModuleSettings $changeModuleSettings): void
    {
        $this->moduleRepository->save($changeModuleSettings->module);
        $this->eventDispatcher->dispatch(new ClearCacheEvent());

        $this->handleTwoFactorAuthorizationChanges($changeModuleSettings);
    }

    private function handleTwoFactorAuthorizationChanges(ChangeModuleSettings $changeModuleSettings): void
    {
        if (
            $changeModuleSettings->module->getName()->equals(ModuleName::fromString('Backend')) &&
            $changeModuleSettings->module->getSetting('2fa_enabled', false)
        ) {
            $users = $this->userRepository->findAll();

            foreach ($users as $user) {
                $user->disableTwoFactorAuthentication();
                $this->userRepository->save($user);
            }
        }
    }
}
