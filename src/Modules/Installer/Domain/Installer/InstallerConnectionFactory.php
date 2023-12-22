<?php

namespace ForkCMS\Modules\Installer\Domain\Installer;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\SQLite\Driver;
use Exception;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;

final class InstallerConnectionFactory extends ConnectionFactory
{
    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $mappingTypes
     */
    public function createConnection(
        array $params,
        Configuration $config = null,
        EventManager $eventManager = null,
        array $mappingTypes = []
    ): Connection {
        try {
            $installationData = InstallerConfiguration::fromCache();
            if (!$installationData->hasStep(InstallerStep::DATABASE)) {
                return $this->getInstallerConnection($params, $config, $eventManager);
            }

            $params['host'] = $installationData->getDatabaseHostname();
            $params['port'] = $installationData->getDatabasePort();
            $params['dbname'] = $installationData->getDatabaseName();
            $params['user'] = $installationData->getDatabaseUsername();
            $params['password'] = $installationData->getDatabasePassword();

            // continue with regular connection creation using new params
            return parent::createConnection($params, $config, $eventManager, $mappingTypes);
        } catch (Exception) {
            return $this->getInstallerConnection($params, $config, $eventManager);
        }
    }

    /** @param array<string, mixed> $params */
    private function getInstallerConnection(
        array $params,
        Configuration $config = null,
        EventManager $eventManager = null
    ): Connection {
        return new Connection($params, new Driver(), $config, $eventManager);
    }
}
