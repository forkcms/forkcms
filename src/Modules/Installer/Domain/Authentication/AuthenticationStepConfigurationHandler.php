<?php

namespace ForkCMS\Modules\Installer\Domain\Authentication;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;

final class AuthenticationStepConfigurationHandler implements CommandHandlerInterface
{
    public function __invoke(AuthenticationStepConfiguration $authenticationStepConfiguration): void
    {
        InstallerConfiguration::toCache(
            InstallerConfiguration::fromCache()->withAuthenticationStep($authenticationStepConfiguration)
        );
    }
}
