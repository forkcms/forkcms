<?php

namespace ForkCMS\Modules\Installer\Domain\Configuration;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Installer\Domain\Authentication\AuthenticationStepConfiguration;
use ForkCMS\Modules\Installer\Domain\Database\DatabaseStepConfiguration;
use ForkCMS\Modules\Installer\Domain\Locale\LocalesStepConfiguration;
use ForkCMS\Modules\Installer\Domain\Module\ModulesStepConfiguration;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Cache\ItemInterface;

final class ConfigurationParser
{
    public function __construct(
        private readonly string $rootDir,
        private readonly ModuleInstallerLocator $moduleInstallerLocator,
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function toDotEnvFile(InstallerConfiguration $installerConfiguration): void
    {
        file_put_contents($this->getDotEnvFilename(), $this->toDotEnv($installerConfiguration));
    }

    public function toDotEnv(InstallerConfiguration $installerConfiguration): string
    {
        $debugEmail = $installerConfiguration->hasDifferentDebugEmail()
            ? $installerConfiguration->getDebugEmail() : $installerConfiguration->getAdminEmail();

        $isOnHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
            || ((int) ($_SERVER['SERVER_PORT'] ?? 80)) === 443;

        return sprintf(
            'FORK_DATABASE_HOST=%1$s
FORK_DATABASE_PORT=%2$s
FORK_DATABASE_NAME=%3$s
FORK_DATABASE_USER=%4$s
FORK_DATABASE_PASSWORD=%5$s
FORK_DEBUG_EMAIL=%6$s
SITE_PROTOCOL=%7$s
SITE_DOMAIN=%8$s
SITE_MULTILINGUAL=%9$s
APP_SECRET=%10$s',
            $installerConfiguration->getDatabaseHostname(),
            $installerConfiguration->getDatabasePort(),
            $installerConfiguration->getDatabaseName(),
            $installerConfiguration->getDatabaseUsername(),
            $installerConfiguration->getDatabasePassword(),
            $debugEmail,
            $isOnHttps ? 'https' : 'http',
            $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '127.0.0.1',
            $installerConfiguration->isMultilingual() ? 'true' : 'false',
            bin2hex(random_bytes(10))
        );
    }

    public function toYamlFile(InstallerConfiguration $installerConfiguration, ?string $path = null): void
    {
        file_put_contents($path ?? $this->getYamlFilename(), $this->toYaml($installerConfiguration));
    }

    public function toYaml(InstallerConfiguration $installerConfiguration): string
    {
        $withCredentials = $installerConfiguration->shouldSaveConfigurationWithCredentials();
        $authentication = AuthenticationStepConfiguration::fromInstallerConfiguration($installerConfiguration);
        $database = DatabaseStepConfiguration::fromInstallerConfiguration($installerConfiguration);
        $locales = LocalesStepConfiguration::fromInstallerConfiguration($installerConfiguration);
        $modules = ModulesStepConfiguration::fromInstallerConfiguration($installerConfiguration);
        $configuration = [
            'admin-email' => $withCredentials ? $authentication->email : null,
            'admin-password' => $withCredentials ? $authentication->password : null,
            'different-debug-email' => $authentication->differentDebugEmail,
            'debug-email' => $authentication->debugEmail,
            'database-hostname' => $withCredentials ? $database->databaseHostname : null,
            'database-port' => $withCredentials ? $database->databasePort : null,
            'database-name' => $withCredentials ? $database->databaseName : null,
            'database-username' => $withCredentials ? $database->databaseUsername : null,
            'database-password' => $withCredentials ? $database->databasePassword : null,
            'multilingual' => $locales->multilingual,
            'locales' => array_map(
                static fn (Locale $locale) => new TaggedValue('fork-cms_locale', $locale->value),
                $locales->locales
            ),
            'default-locale' => new TaggedValue('fork-cms_locale', $locales->defaultLocale->value),
            'interface-locales' => array_map(
                static fn (Locale $locale) => new TaggedValue('fork-cms_locale', $locale->value),
                $locales->userLocales
            ),
            'default-interface-locale' => new TaggedValue('fork-cms_locale', $locales->defaultUserLocale->value),
            'modules' => array_map(
                static fn (ModuleName $moduleName) => new TaggedValue('fork-cms_module', $moduleName->getName()),
                $modules->modules
            ),
            'install-example-data' => $modules->installExampleData,
        ];

        return Yaml::dump($configuration);
    }

    public function loadFromYaml(InstallerConfiguration $installerConfiguration, string $yamlComfiguration): void
    {
        $configuration = Yaml::parse($yamlComfiguration, Yaml::PARSE_CUSTOM_TAGS);
        $moduleNameMap = $this->moduleInstallerLocator->getAllModuleNames();
        $configuration['modules'] = array_map(
            static function (TaggedValue $taggedModule) use ($moduleNameMap): ModuleName {
                if (!array_key_exists($taggedModule->getValue(), $moduleNameMap)) {
                    throw new InvalidArgumentException(
                        'The module "' . $taggedModule->getValue() . '" does not exist.'
                    );
                }

                return $moduleNameMap[$taggedModule->getValue()];
            },
            $configuration['modules'] ?? [],
        );
        $configuration['locales'] = array_map(
            static fn (TaggedValue $taggedLocale) => Locale::from($taggedLocale->getValue()),
            $configuration['locales'],
        );
        $configuration['default-locale'] = Locale::from($configuration['default-locale']->getValue());
        $configuration['interface-locales'] = array_map(
            static fn (TaggedValue $taggedLocale) => Locale::from($taggedLocale->getValue()),
            $configuration['interface-locales'],
        );
        $configuration['default-interface-locale'] = Locale::from(
            $configuration['default-interface-locale']->getValue()
        );

        $installerConfiguration->withLocaleStep(LocalesStepConfiguration::fromArray($configuration));
        $installerConfiguration->withModulesStep(
            ModulesStepConfiguration::fromArray($configuration),
            $this->moduleInstallerLocator,
            $this->commandBus
        );
        if (($_ENV['FORK_DATABASE_HOST'] ?? '') !== '') {
            $installerConfiguration->withDatabaseStep(DatabaseStepConfiguration::fromEnv());
        } else {
            $installerConfiguration->withDatabaseStep(DatabaseStepConfiguration::fromArray($configuration));
        }
        $installerConfiguration->withAuthenticationStep(AuthenticationStepConfiguration::fromArray($configuration));
    }

    public function loadFromFile(): InstallerConfiguration
    {
        $installerConfiguration = InstallerConfiguration::fromCache();

        if ($this->configurationFileExists()) {
            $this->loadFromYaml($installerConfiguration, file_get_contents($this->getYamlFilename()));
        }

        InstallerConfiguration::toCache($installerConfiguration);

        return $installerConfiguration;
    }

    private function getCache(): FilesystemAdapter
    {
        return new FilesystemAdapter(static::class, 3600);
    }

    public function fromCache(): InstallerConfiguration
    {
        return $this->getCache()->get(
            'installer.configuration',
            function (ItemInterface $item) {
                $item->expiresAfter(3600);
                $configuration = new InstallerConfiguration();
                $item->set($configuration);

                return $configuration;
            }
        );
    }

    public function toCache(InstallerConfiguration $installerConfiguration): void
    {
        $cache = $this->getCache();
        /** @var CacheItem $cacheItem */
        $cacheItem = $cache->getItem('installer.configuration');
        $cacheItem->expiresAfter(3600);
        $cacheItem->set($installerConfiguration);
        $cache->save($cacheItem);
    }

    public function configurationFileExists(): bool
    {
        return file_exists($this->getYamlFilename());
    }

    private function getYamlFilename(): string
    {
        $yamlPath = $_ENV['FORK_INSTALLATION_CONFIGURATION_PATH'] ?? '/fork-cms-installation-configuration.yaml';

        return $this->rootDir . $yamlPath;
    }

    private function getDotEnvFilename(): string
    {
        return $this->rootDir . ($_ENV['FORK_INSTALLATION_LOCALE_ENV_PATH'] ?? '/.env.local');
    }
}
