<?php

namespace ForkCMS\Core\Domain\Kernel;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

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

    final protected function configureContainer(ContainerConfigurator $container): void
    {
        if ($this->isInstalled()) {
            $this->configureLiveContainer($container);

            return;
        }

        $this->configureInstallerContainer($container);
    }

    final protected function buildContainer(): ContainerBuilder
    {
        $container = parent::buildContainer();

        // @TODO: Implement loading core extension and the installer extension

        $this->registerModuleExtensions($container);

        return $container;
    }

    private function configureLiveContainer(ContainerConfigurator $container): void
    {
        $container->import(self::ROOT_DIR . 'config/{packages}/*.yaml');
        $container->import(self::ROOT_DIR . 'config/{packages}/' . $this->environment . '/*.yaml');

        $container->import(self::ROOT_DIR . 'config/services.yaml');
        $container->import(self::ROOT_DIR . 'config/{services}_' . $this->environment . '.yaml');
    }

    private function configureInstallerContainer(ContainerConfigurator $container): void
    {
        $container->import(self::ROOT_DIR . 'config/{packages}/*.yaml');
        $container->import(self::ROOT_DIR . 'config/{packages}/install/*.yaml');
        if ($this->environment === 'test_install') {
            $container->import(self::ROOT_DIR . 'config/{packages}/test_install/*.yaml');
        }

        $container->import(self::ROOT_DIR . 'config/{services}_install.yaml');
        if ($this->environment === 'test_install') {
            $container->import(self::ROOT_DIR . 'config/{services}_test_install.yaml');
        }
    }

    private function registerModuleExtensions(ContainerBuilder $container): void
    {
        // @TODO
    }

    final public function getContainerClass(): string
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
