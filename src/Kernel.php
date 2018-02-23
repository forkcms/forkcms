<?php

namespace App;

use App\Backend\DependencyInjection\BackendExtension;
use Spoon;
use SpoonDatabaseException;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use PDOException;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /** @var Request We need this to check if a module is being installed */
    private $request;

    /**
     * @ForkCMS: Constructor.
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
     * @ForkCMS
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
     * @ForkCMS: Boot and define the Fork Constants.
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
     * @ForkCMS: This will disappear in time in favour of container-driven parameters.
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
         * @deprecated SPOON_* constants are deprecated in favour of Spoon::set*().
         * Will be removed in the next major release.
         */
        defined('PATH_WWW') || define('PATH_WWW', realpath($container->getParameter('site.path_www')) . '/..');

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
        defined('FRONTEND_THEMES_PATH') || define('FRONTEND_THEMES_PATH', FRONTEND_PATH . '/Themes');
        defined('FRONTEND_CORE_PATH') || define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/Core');
        defined('FRONTEND_MODULES_PATH') || define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/Modules');
        defined('FRONTEND_FILES_PATH') || define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/Files');
        defined('FRONTEND_FILES_URL') || define('FRONTEND_FILES_URL', '/src/Frontend/Files');
        defined('FRONTEND_CORE_URL') || define('FRONTEND_CORE_URL', '/src/Frontend/Core');
        defined('FRONTEND_CACHE_URL') || define('FRONTEND_CACHE_URL', '/src/Frontend/Cache');
    }

    /**
     * @ForkCMS: Builds the service container.
     *
     * @return ContainerBuilder The compiled service container
     * @throws \Exception
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

    /**
     * @ForkCMS
     *
     * @return array
     */
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

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    /**
     * @ForkCMS
     *
     * @param ContainerBuilder $containerBuilder
     * @return array
     * @throws \Exception
     */
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

    /**
     * @ForkCMS
     */
    protected function initializeContainer(): void
    {
        // remove the cache dir when installing a module to trigger rebuilding the kernel
        if ($this->isInstallingModule()) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($this->getCacheDir().'/'.$this->getContainerClass().'.php');
        }

        parent::initializeContainer();
    }

    /**
     * @ForkCMS
     *
     * @return bool
     */
    private function isInstallingModule(): bool
    {
        return preg_match('/\/private(\/\w\w)?\/extensions\/install_module\?/', $this->request->getRequestUri())
            && $this->request->query->has('module')
            && in_array($this->request->query->get('module'), $this->getAllPossibleModuleNames());
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');

        // @ForkCMS
        if ($this->environment !== 'install') {
            $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        }

        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');

        // @ForkCMS
        $routes->import($confDir.'/{routes}_'.$this->environment.self::CONFIG_EXTS, '/', 'glob');

        // @ForkCMS
        if ($this->environment !== 'install') {
            $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
        }
    }
}
