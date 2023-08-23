<?php

namespace ForkCMS\Core\Domain\Kernel;

use ForkCMS\Core\DependencyInjection\CoreExtension;
use ForkCMS\Core\Domain\PDO\ForkConnection;
use ForkCMS\Modules\Extensions\Domain\Module\InstalledModules;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Installer\DependencyInjection\InstallerExtension;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const ROOT_DIR = __DIR__ . '/../../../../';

    private bool $isInstalled;

    public function __construct(string $environment, bool $debug)
    {
        $this->isInstalled = $environment !== 'test_install' && file_exists(self::ROOT_DIR . '.env.local');

        if (!$this->isInstalled) {
            $environment = str_contains($environment, 'test') ? 'test_install' : 'install';
        }

        parent::__construct($environment, $debug);
    }

    private function isInstalled(): bool
    {
        return $this->isInstalled || !str_ends_with($this->environment, 'install');
    }

    protected function configureContainer(ContainerConfigurator $containerConfigurator): void
    {
        if ($this->isInstalled()) {
            $this->configureLiveContainer($containerConfigurator);

            return;
        }

        $this->configureInstallerContainer($containerConfigurator);
    }

    protected function buildContainer(): ContainerBuilder
    {
        $container = parent::buildContainer();

        $container->registerExtension(new CoreExtension());
        $container->registerExtension(new InstallerExtension());

        $this->registerModuleExtensions($container);

        return $container;
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        if ($this->isInstalled()) {
            $this->configureLiveRoutes($routes);

            return;
        }

        $this->configureInstallerRoutes($routes);
    }

    private function configureLiveContainer(ContainerConfigurator $containerConfigurator): void
    {
        $containerConfigurator->import(self::ROOT_DIR . 'config/{packages}/*.yaml');
        $containerConfigurator->import(self::ROOT_DIR . 'config/{packages}/' . $this->environment . '/*.yaml');

        $containerConfigurator->import(self::ROOT_DIR . 'config/services.yaml');
        $containerConfigurator->import(self::ROOT_DIR . 'config/{services}_' . $this->environment . '.yaml');
    }

    private function configureInstallerContainer(ContainerConfigurator $containerConfigurator): void
    {
        $containerConfigurator->import(self::ROOT_DIR . 'config/{packages}/*.yaml');
        $containerConfigurator->import(self::ROOT_DIR . 'config/{packages}/install/*.yaml');
        if ($this->environment === 'test_install') {
            $containerConfigurator->import(self::ROOT_DIR . 'config/{packages}/test_install/*.yaml');
        }

        $containerConfigurator->import(self::ROOT_DIR . 'config/{services}_install.yaml');
        if ($this->environment === 'test_install') {
            $containerConfigurator->import(self::ROOT_DIR . 'config/{services}_test_install.yaml');
        }
    }

    private function configureLiveRoutes(RoutingConfigurator $routes): void
    {
        $websiteLocales = ForkConnection::get()->getWebsiteLocales();
        $defaults = [
            '_locale' => array_search(true, $websiteLocales),
        ];
        $requirements = [
            '_locale' => implode('|', array_keys($websiteLocales)),
        ];
        $importWithDefaultsAndRequirements = static function ($resource) use (
            $routes,
            $defaults,
            $requirements,
        ): void {
            $routes->import($resource)
                ->requirements($requirements)
                ->defaults($defaults);
        };
        $importWithDefaultsAndRequirements(self::ROOT_DIR . 'config/{routes}/' . $this->environment . '/*.yaml');
        $importWithDefaultsAndRequirements(self::ROOT_DIR . 'config/{routes}/*.yaml');

        if (is_file(self::ROOT_DIR . 'config/routes.yaml')) {
            $importWithDefaultsAndRequirements(self::ROOT_DIR . 'config/routes.yaml');
        }
    }

    private function configureInstallerRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(self::ROOT_DIR . 'config/{routes}/install/*.yaml');
    }

    /** @return ModuleName[] */
    protected function getInstalledModules(ContainerBuilder $container): array
    {
        if ($container->getParameter('fork.is_installed') === false) {
            return ModuleInstallerLocator::moduleNamesFromFileSystem();
        }

        $modules = InstalledModules::fromContainer($container)();
        if ($this->isInstallingModule()) {
            $modules[] = ModuleName::fromString($_POST['action']['id']);
        }

        return $modules;
    }

    private function registerModuleExtensions(ContainerBuilder $container): void
    {
        $filesystem = new Filesystem();
        foreach ($this->getInstalledModules($container) as $module) {
            $finder = new Finder();
            $moduleDirectory = self::ROOT_DIR . '/src/Modules/' . $module;

            if (!$filesystem->exists($moduleDirectory)) {
                continue;
            }

            $domainDirectory = $moduleDirectory . '/Domain';
            if ($filesystem->exists($domainDirectory)) {
                $container->prependExtensionConfig(
                    'doctrine',
                    [
                        'orm' => [
                            'mappings' => [
                                $module->getName() => [
                                    'type' => 'annotation',
                                    'is_bundle' => false,
                                    'dir' => $domainDirectory,
                                    'prefix' => 'ForkCMS\\Modules\\' . $module . '\\Domain',
                                ],
                                $module->getName() => [
                                    'type' => 'attribute',
                                    'is_bundle' => false,
                                    'dir' => $domainDirectory,
                                    'prefix' => 'ForkCMS\\Modules\\' . $module . '\\Domain',
                                ],
                            ],
                        ],
                    ]
                );
            }

            $dependencyInjectionNamespace = 'ForkCMS\\Modules\\' . $module . '\\DependencyInjection\\';
            $dependencyInjectionExtension = $dependencyInjectionNamespace . $module . 'Extension';

            if (class_exists($dependencyInjectionExtension)) {
                $container->registerExtension(new $dependencyInjectionExtension());
            }

            $compilerPassDirectory = $moduleDirectory . '/DependencyInjection/CompilerPass/';
            if ($filesystem->exists($compilerPassDirectory)) {
                $compilerPassNamespace = $dependencyInjectionNamespace . 'CompilerPass\\';
                foreach ($finder->in($compilerPassDirectory)->files()->name('*.php') as $compilerPassFile) {
                    $compilerPassFQCN = $compilerPassNamespace . substr($compilerPassFile->getFilename(), 0, -4);
                    $compilerPass = new $compilerPassFQCN();
                    $type = method_exists($compilerPass, 'getType')
                        ? $compilerPass->getType() : PassConfig::TYPE_BEFORE_OPTIMIZATION;
                    $priority = method_exists($compilerPass, 'getPriority')
                        ? $compilerPass->getPriority() : 0;
                    $container->addCompilerPass(
                        $compilerPass,
                        $type,
                        $priority
                    );
                }
            }
        }

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(
            new MergeExtensionConfigurationPass(array_keys($container->getExtensions()))
        );
    }

    public function getContainerClass(): string
    {
        return parent::getContainerClass();
    }

    protected function initializeContainer(): void
    {
        $class = $this->getContainerClass();
        $buildDir = $this->getBuildDir();
        $cache = new ConfigCache($buildDir . '/' . $class . '.php', $this->debug);
        if ($this->isInstallingModule() && $cache->isFresh()) {
            unlink($cache->getPath());
        }
        parent::initializeContainer();
    }

    private function isInstallingModule(): bool
    {
        return str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/extensions/module_install');
    }
}
