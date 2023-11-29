<?php

namespace ForkCMS\Modules\Extensions\Domain\Module\Command;

use ForkCMS\Core\Domain\Kernel\Command\ClearContainerCache;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use Symfony\Component\Messenger\MessageBusInterface;

final class ChangeModuleSettingsHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ModuleRepository $moduleRepository,
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(ChangeModuleSettings $changeModuleSettings): void
    {
        $this->moduleRepository->save($changeModuleSettings->module);
        $this->commandBus->dispatch(new ClearContainerCache());
    }
}
