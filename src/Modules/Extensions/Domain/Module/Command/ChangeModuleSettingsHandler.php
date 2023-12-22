<?php

namespace ForkCMS\Modules\Extensions\Domain\Module\Command;

use ForkCMS\Core\Domain\Kernel\Event\ClearCacheEvent;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ChangeModuleSettingsHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ModuleRepository $moduleRepository,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function __invoke(ChangeModuleSettings $changeModuleSettings): void
    {
        $this->moduleRepository->save($changeModuleSettings->module);
        $this->eventDispatcher->dispatch(new ClearCacheEvent());
    }
}
