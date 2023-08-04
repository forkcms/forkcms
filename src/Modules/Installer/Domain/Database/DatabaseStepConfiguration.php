<?php

namespace ForkCMS\Modules\Installer\Domain\Database;

use ForkCMS\Core\Domain\PDO\ForkConnection;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStep;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStepConfiguration;
use Symfony\Component\Validator\Constraints as Assert;

final class DatabaseStepConfiguration implements InstallerStepConfiguration
{
    /** @Assert\NotBlank */
    public ?string $databaseHostname;

    /** @Assert\NotBlank */
    public ?string $databaseUsername;

    /** @Assert\NotBlank */
    public ?string $databasePassword;

    /** @Assert\NotBlank */
    public ?string $databaseName;

    /**
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value="1")
     * @Assert\LessThanOrEqual(value="65535")
     */
    public int $databasePort;

    public function __construct(
        ?string $databaseHostname = null,
        ?string $databaseUsername = null,
        ?string $databasePassword = null,
        ?string $databaseName = null,
        int $databasePort = 3306
    ) {
        $defaultName = $this->getDefaultName();
        $this->databaseHostname = $databaseHostname ?? $_SERVER['MYSQL_HOST'] ?? '127.0.0.1';
        $this->databaseUsername = $databaseUsername ?? $defaultName;
        $this->databasePassword = $databasePassword ?? $defaultName;
        $this->databaseName = $databaseName ?? $defaultName;
        $this->databasePort = $databasePort;
    }

    private function getDefaultName(): string
    {
        $host = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
        if (str_starts_with($host, '127.0.0.1') || str_starts_with($host, 'localhost')) {
            return 'forkcms';
        }

        $chunks = explode('.', $host);
        array_pop($chunks);

        return implode('_', $chunks);
    }

    public static function fromArray(array $configuration): static
    {
        return new self(
            $configuration['database-hostname'],
            $configuration['database-username'],
            $configuration['database-password'],
            $configuration['database-name'],
            $configuration['database-port'],
        );
    }

    public static function fromInstallerConfiguration(InstallerConfiguration $installerConfiguration): static
    {
        if (!$installerConfiguration->hasStep(self::getStep())) {
            return new self();
        }

        return new self(
            $installerConfiguration->getDatabaseHostname(),
            $installerConfiguration->getDatabaseUsername(),
            $installerConfiguration->getDatabasePassword(),
            $installerConfiguration->getDatabaseName(),
            $installerConfiguration->getDatabasePort()
        );
    }

    public function canConnectToDatabase(): bool
    {
        return ForkConnection::testConnection(
            'mysql',
            $this->databaseHostname,
            $this->databasePort,
            $this->databaseName,
            $this->databaseUsername,
            $this->databasePassword
        );
    }

    public static function getStep(): InstallerStep
    {
        return InstallerStep::database;
    }
}
