<?php

namespace ForkCMS\Modules\OAuth\Domain\Settings\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class UpdateModuleSettingsHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ModuleSettings $moduleSettings)
    {
    }

    public function __invoke(UpdateModuleSettings $updateSettings): void
    {
        $this->moduleSettings->set(
            ModuleName::fromString('OAuth'),
            'client_id',
            $updateSettings->clientId
        );

        $this->moduleSettings->set(
            ModuleName::fromString('OAuth'),
            'client_secret',
            $updateSettings->clientSecret
        );

        $this->moduleSettings->set(
            ModuleName::fromString('OAuth'),
            'tenant',
            $updateSettings->tenant
        );

        $this->moduleSettings->set(
            ModuleName::fromString('OAuth'),
            'enabled',
            $updateSettings->enabled
        );
    }
}
