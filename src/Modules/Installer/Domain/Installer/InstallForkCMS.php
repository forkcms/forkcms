<?php

namespace ForkCMS\Modules\Installer\Domain\Installer;

use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;

final class InstallForkCMS
{
    public function __construct(private InstallerConfiguration $installerConfiguration)
    {
    }

    public function getInstallerConfiguration(): InstallerConfiguration
    {
        return $this->installerConfiguration;
    }
}
