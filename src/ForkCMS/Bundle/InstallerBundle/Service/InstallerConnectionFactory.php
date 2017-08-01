<?php

namespace ForkCMS\Bundle\InstallerBundle\Service;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use ForkCMS\Bundle\InstallerBundle\Controller\InstallerController;
use ForkCMS\Bundle\InstallerBundle\DBAL\InstallerConnection;
use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;
use Doctrine\DBAL\Exception\ConnectionException;

class InstallerConnectionFactory extends ConnectionFactory
{
    public function createConnection(
        array $params,
        Configuration $config = null,
        EventManager $eventManager = null,
        array $mappingTypes = []
    ): Connection {
        try {
            $installationData = $this->getInstallationData();
            if ($installationData->getDatabaseHostname() === null) {
                return $this->getInstallerConnection($params, $config, $eventManager);
            }

            $params['host'] = $installationData->getDatabaseHostname();
            $params['port'] = $installationData->getDatabasePort();
            $params['dbname'] = $installationData->getDatabaseName();
            $params['user'] = $installationData->getDatabaseUsername();
            $params['password'] = $installationData->getDatabasePassword();

            //continue with regular connection creation using new params
            return parent::createConnection($params, $config, $eventManager, $mappingTypes);
        } catch (ConnectionException $e) {
            return $this->getInstallerConnection($params, $config, $eventManager);
        }
    }

    private function getInstallationData(): InstallationData
    {
        if (InstallerController::$installationData instanceof InstallationData) {
            return InstallerController::$installationData;
        }

        return new InstallationData();
    }

    private function getInstallerConnection(
        array $params,
        Configuration $config = null,
        EventManager $eventManager = null
    ): InstallerConnection {
        $normalConnection = DriverManager::getConnection($params, $config, $eventManager);

        return new InstallerConnection($params, $normalConnection->getDriver(), $config, $eventManager);
    }
}
