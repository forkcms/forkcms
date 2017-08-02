<?php

namespace ForkCMS\App;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use PDOException;
use Spoon;
use SpoonDatabaseException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Backend\DependencyInjection\BackendExtension;

/**
 * The Kernel provides a proper way to load an environment and DI container.
 * It also handles requests and responses.
 */
abstract class Kernel extends BaseKernel
{
    /** @var Request We need this to check if a module is being installed */
    private $request;

    /**
     * Constructor.
     *
     * @param string $environment The environment
     * @param bool $enableDebug Whether to enable debugging or not
     *
     * @api
     */
    public function __construct(string $environment, bool $enableDebug)
    {
        $this->request = Request::createFromGlobals();

        parent::__construct($environment, $enableDebug);
        $this->boot();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true): Response
    {
        // boot if it hasn't booted yet
        $this->boot();

        return $this->getHttpKernel()->handle($request, $type, $catch);
    }

    /**
     * Boot and define the Fork Constants.
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        parent::boot();

        // define Fork constants
        $this->defineForkConstants();
    }

    /**
     * This will disappear in time in favour of container-driven parameters.
     *
     * @deprecated
     */
    public function defineForkConstants(): void
    {
        $container = $this->getContainer();

        Spoon::setDebug($container->getParameter('kernel.debug'));
        Spoon::setDebugEmail($container->getParameter('fork.debug_email'));
        Spoon::setDebugMessage($container->getParameter('fork.debug_message'));
        Spoon::setCharset($container->getParameter('kernel.charset'));

        /**
         * @deprecated SPOON_* constants are deprecated in favor of Spoon::set*().
         * Will be removed in the next major release.
         */
        defined('PATH_WWW') || define('PATH_WWW', realpath($container->getParameter('site.path_www')));

        defined('SITE_DEFAULT_LANGUAGE') || define('SITE_DEFAULT_LANGUAGE', $container->getParameter('site.default_language'));
        defined('SITE_DEFAULT_TITLE') || define('SITE_DEFAULT_TITLE', $container->getParameter('site.default_title'));
        defined('SITE_MULTILANGUAGE') || define('SITE_MULTILANGUAGE', $container->getParameter('site.multilanguage'));
        defined('SITE_DOMAIN') || define('SITE_DOMAIN', $container->getParameter('site.domain'));
        defined('SITE_PROTOCOL') || define('SITE_PROTOCOL', $container->getParameter('site.protocol'));
        defined('SITE_URL') || define('SITE_URL', SITE_PROTOCOL . '://' . SITE_DOMAIN);

        defined('FORK_VERSION') || define('FORK_VERSION', $container->getParameter('fork.version'));

        defined('ACTION_GROUP_TAG') || define('ACTION_GROUP_TAG', $container->getParameter('action.group_tag'));
        defined('ACTION_RIGHTS_LEVEL') || define('ACTION_RIGHTS_LEVEL', $container->getParameter('action.rights_level'));

        defined('BACKEND_PATH') || define('BACKEND_PATH', PATH_WWW . '/src/Backend');
        defined('BACKEND_CACHE_PATH') || define('BACKEND_CACHE_PATH', BACKEND_PATH . '/Cache');
        defined('BACKEND_CORE_PATH') || define('BACKEND_CORE_PATH', BACKEND_PATH . '/Core');
        defined('BACKEND_MODULES_PATH') || define('BACKEND_MODULES_PATH', BACKEND_PATH . '/Modules');
        defined('BACKEND_CORE_URL') || define('BACKEND_CORE_URL', '/src/Backend/Core');
        defined('BACKEND_CACHE_URL') || define('BACKEND_CACHE_URL', '/src/Backend/Cache');

        defined('FRONTEND_PATH') || define('FRONTEND_PATH', PATH_WWW . '/src/Frontend');
        defined('FRONTEND_CACHE_PATH') || define('FRONTEND_CACHE_PATH', FRONTEND_PATH . '/Cache');
        defined('FRONTEND_CORE_PATH') || define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/Core');
        defined('FRONTEND_MODULES_PATH') || define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/Modules');
        defined('FRONTEND_FILES_PATH') || define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/Files');
        defined('FRONTEND_FILES_URL') || define('FRONTEND_FILES_URL', '/src/Frontend/Files');
        defined('FRONTEND_CORE_URL') || define('FRONTEND_CORE_URL', '/src/Frontend/Core');
        defined('FRONTEND_CACHE_URL') || define('FRONTEND_CACHE_URL', '/src/Frontend/Cache');
    }

    /**
     * Builds the service container.
     *
     * @throws \RuntimeException
     *
     * @return ContainerBuilder The compiled service container
     */
    protected function buildContainer()
    {
        $container = parent::buildContainer();

        $installedModules = $this->getInstalledModules($container);

        $container->setParameter('installed_modules', $installedModules);

        foreach ($installedModules as $module) {
            $class = 'Backend\\Modules\\' . $module . '\\DependencyInjection\\' . $module . 'Extension';

            if (class_exists($class)) {
                $container->registerExtension(new $class());
            }
        }

        $container->registerExtension(new BackendExtension());

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(
            new MergeExtensionConfigurationPass(array_keys($container->getExtensions()))
        );

        return $container;
    }

    private function getInstalledModules(ContainerBuilder $containerBuilder): array
    {
        // on installation all modules should be loaded
        if ($this->environment === 'install' || $this->environment === 'test') {
            return $this->getAllPossibleModuleNames();
        }

        $moduleNames = [];
        if ($this->isInstallingModule()) {
            $moduleNames[] = $this->request->query->get('module');
        }

        try {
            $moduleNames = array_merge(
                $moduleNames,
                (array) $containerBuilder->get('database')->getColumn(
                    'SELECT name FROM modules'
                )
            );
        } catch (SpoonDatabaseException $e) {
            $moduleNames = [];
        } catch (PDOException $e) {
            // fork is probably not installed yet
            $moduleNames = [];
        }

        if (empty($moduleNames)) {
            return $this->getAllPossibleModuleNames();
        }

        return $moduleNames;
    }

    private function isInstallingModule(): bool
    {
        return preg_match('/\/private(\/\w\w)?\/extensions\/install_module\?/', $this->request->getRequestUri())
               && $this->request->query->has('module')
               && in_array($this->request->query->get('module'), $this->getAllPossibleModuleNames());
    }

    private function getAllPossibleModuleNames(): array
    {
        $moduleNames = [];
        $finder = new Finder();

        $directories = $finder->directories()->in(__DIR__ . '/../src/Backend/Modules')->depth(0);

        foreach ($directories->getIterator() as $directory) {
            $moduleNames[] = $directory->getFilename();
        }

        return $moduleNames;
    }

    protected function initializeContainer(): void
    {
        // remove the cache dir when installing a module to trigger rebuilding the kernel
        if ($this->isInstallingModule()) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($this->getCacheDir().'/'.$this->getContainerClass().'.php');
        }

        parent::initializeContainer();
    }

    public function getLogDir(): string
    {
        return dirname(__DIR__).'/var/logs/' . $this->environment;
    }

    public function getCacheDir(): string
    {
        return dirname(__DIR__) . '/var/cache/' . $this->environment;
    }
}
