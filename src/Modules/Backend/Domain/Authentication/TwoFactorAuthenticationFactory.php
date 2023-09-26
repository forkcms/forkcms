<?php

namespace ForkCMS\Modules\Backend\Domain\Authentication;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;

class TwoFactorAuthenticationFactory
{
    public function __construct(private readonly ModuleSettings $moduleSettings)
    {
    }

    public function secret(): string
    {
        return $this->moduleSettings->get(
            ModuleName::fromString('Backend'),
            '2fa_secret',
            ''
        );
    }

    public function trustedDevices(): bool
    {
        return $this->moduleSettings->get(
            ModuleName::fromString('Backend'),
            'trusted_devices_enabled',
            false
        );
    }

    public function enabled(): bool
    {
        return $this->moduleSettings->get(
            ModuleName::fromString('Backend'),
            '2fa_enabled',
            false
        );
    }
}
