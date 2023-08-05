<?php

namespace ForkCMS\Modules\Extensions\Domain\Module\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;

final class ChangeModuleSettingsHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ModuleRepository $moduleRepository,
        private readonly ModuleSettings $moduleSettings,
    ) {
    }

    public function __invoke(ChangeModuleSettings $changeModuleSettings): void
    {
        $this->moduleRepository->save($changeModuleSettings->module);
        $this->moduleSettings->invalidateCache();
    }
}
