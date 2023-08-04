<?php

namespace ForkCMS\Modules\Installer\Domain\Database;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;

final class DatabaseStepConfigurationHandler implements CommandHandlerInterface
{
    public function __invoke(DatabaseStepConfiguration $databaseStepConfiguration): void
    {
        InstallerConfiguration::toCache(
            InstallerConfiguration::fromCache()->withDatabaseStep($databaseStepConfiguration)
        );
    }
}
