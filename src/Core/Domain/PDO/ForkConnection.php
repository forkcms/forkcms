<?php

namespace ForkCMS\Core\Domain\PDO;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * @internal please use doctrine, this is only for use while the kernel is building and we can't use doctrine yet
 */
final class ForkConnection extends PDO
{
    /** @var self[] */
    private static array $instances = [];

    public static function get(string $environment = 'prod'): self
    {
        $dsn = sprintf(
            '%1$s:host=%2$s;port=%3$s;dbname=%4$s',
            $_ENV['FORK_DATABASE_DRIVER'],
            $_ENV['FORK_DATABASE_HOST'],
            $_ENV['FORK_DATABASE_PORT'],
            $_ENV['FORK_DATABASE_NAME'],
        );

        if (!(self::$instances[$environment] ?? null) instanceof self) {
            self::$instances[$environment] = new self(
                $dsn,
                $_ENV['FORK_DATABASE_USER'],
                $_ENV['FORK_DATABASE_PASSWORD']
            );
        }

        return self::$instances[$environment];
    }

    /** @return ModuleName[] */
    public function getInstalledModules(): array
    {
        $modulesQuery = $this->query('SELECT name from extensions__module');
        if (!$modulesQuery->execute()) {
            $modulesQuery->closeCursor();
            throw new RuntimeException('Cannot get installed modules from database');
        }

        $installedModules = $modulesQuery->fetchAll(PDO::FETCH_COLUMN, 0);
        $modulesQuery->closeCursor();

        return array_map(
            static fn (string $moduleName): ModuleName => ModuleName::fromString($moduleName),
            $installedModules
        );
    }

    /** @return array<string, bool> true for the default website locale */
    public function getEnabledLocales(): array
    {
        $query = $this->query(
            'SELECT locale, isDefaultForWebsite
                FROM internationalisation__installed_locale
                WHERE isEnabledForWebsite = true OR isEnabledForUser = true'
        );
        $enabledLocales = $query->fetchAll(PDO::FETCH_KEY_PAIR);
        $query->closeCursor();

        return array_map(static fn (bool $isEnabled): bool => $isEnabled, $enabledLocales);
    }

    /** @return array<string, bool> true for the default website locale */
    public function getWebsiteLocales(): array
    {
        $query = $this->query(
            'SELECT locale, isDefaultForWebsite
                FROM internationalisation__installed_locale
                WHERE isEnabledForWebsite = true'
        );
        $locales = $query->fetchAll(PDO::FETCH_KEY_PAIR);
        $query->closeCursor();

        return array_map(static fn (bool $isEnabled): bool => $isEnabled, $locales);
    }

    /** @return string[] */
    public function getTables(): array
    {
        $query = $this->query('SHOW TABLES');
        $tables = $query->fetchAll(self::FETCH_COLUMN, 0);
        $query->closeCursor();

        return $tables;
    }

    public static function testConnection(
        string $driver,
        string $host,
        int $port,
        string $database,
        string $user,
        string $password
    ): bool {
        try {
            $connection = new self(
                sprintf('%1$s:host=%2$s;port=%3$d;dbname=%4$s', $driver, $host, $port, $database),
                $user,
                $password
            );
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $tableName = 'test' . str_replace('.', '', (string) microtime(true));

            return $connection->query('DROP TABLE IF EXISTS ' . $tableName) instanceof PDOStatement
                && $connection->query('CREATE TABLE ' . $tableName . ' (id int(11) NOT NULL)') instanceof PDOStatement
                && $connection->query('DROP TABLE ' . $tableName) instanceof PDOStatement;
        } catch (PDOException) {
            return false;
        }
    }

    public function getActiveTheme(): string
    {
        $themeQuery = $this->query('SELECT name FROM extensions__theme WHERE active = 1');
        if (!$themeQuery->execute()) {
            $themeQuery->closeCursor();
            if (str_contains($_ENV['FORK_INSTALLER_THEME'], '/')) {
                throw new RuntimeException('Theme name should not contain a slash');
            }

            return $_ENV['FORK_INSTALLER_THEME'];
        }

        $theme = $themeQuery->fetch(PDO::FETCH_COLUMN, 0);
        $themeQuery->closeCursor();

        return $theme;
    }
}
