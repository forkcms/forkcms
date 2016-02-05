<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * The Kernel provides a proper way to load an environment and DI container.
 * It also handles requests and responses.
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
abstract class Kernel extends BaseKernel implements KernelInterface
{
    /**
     * Constructor.
     *
     * @param string $environment The environment
     * @param bool   $debug       Whether to enable debugging or not
     *
     * @api
     */
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
        $this->boot();
        // define Fork constants
        $this->defineForkConstants();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return $this->getHttpKernel()->handle($request, $type, $catch);
    }

    /**
     * This will disappear in time in favour of container-driven parameters.
     * @deprecated
     */
    public function defineForkConstants()
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
        defined('SPOON_DEBUG') || define('SPOON_DEBUG', $container->getParameter('kernel.debug'));
        defined('SPOON_DEBUG_EMAIL') || define('SPOON_DEBUG_EMAIL', $container->getParameter('fork.debug_email'));
        defined('SPOON_DEBUG_MESSAGE') || define('SPOON_DEBUG_MESSAGE', $container->getParameter('fork.debug_message'));
        defined('SPOON_CHARSET') || define('SPOON_CHARSET', $container->getParameter('kernel.charset'));

        defined('PATH_WWW') || define('PATH_WWW', $container->getParameter('site.path_www'));
        defined('PATH_LIBRARY') || define('PATH_LIBRARY', $container->getParameter('site.path_library'));

        defined('SITE_DEFAULT_LANGUAGE') || define('SITE_DEFAULT_LANGUAGE', $container->getParameter('site.default_language'));
        defined('SITE_DEFAULT_TITLE') || define('SITE_DEFAULT_TITLE', $container->getParameter('site.default_title'));
        defined('SITE_MULTILANGUAGE') || define('SITE_MULTILANGUAGE', $container->getParameter('site.multilanguage'));
        defined('SITE_DOMAIN') || define('SITE_DOMAIN', $container->getParameter('site.domain'));
        defined('SITE_PROTOCOL') || define('SITE_PROTOCOL', $container->getParameter('site.protocol'));
        defined('SITE_URL') || define('SITE_URL', SITE_PROTOCOL . '://' . SITE_DOMAIN);

        defined('FORK_VERSION') || define('FORK_VERSION', $container->getParameter('fork.version'));

        defined('ACTION_GROUP_TAG') || define('ACTION_GROUP_TAG', $container->getParameter('action.group_tag'));
        defined('ACTION_RIGHTS_LEVEL') || define('ACTION_RIGHTS_LEVEL', $container->getParameter('action.rights_level'));
    }

    /**
     * Builds the service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder The compiled service container
     *
     * @throws \RuntimeException
     */
    protected function buildContainer()
    {
        $container = parent::buildContainer();

        try {
            $installedModules = $container->get('database')->getColumn(
                'SELECT name FROM modules'
            );
        } catch (\SpoonDatabaseException $e) {
            $installedModules = array();
        } catch (\PDOException $e) {
            // fork is probably not installed yet
            $installedModules = array();
        }

        $container->setParameter('installed_modules', $installedModules);

        $extensions = array();
        foreach ($installedModules as $module) {
            $class = 'Backend\\Modules\\' . $module . '\\DependencyInjection\\' . $module . 'Extension';

            if (class_exists($class)) {
                $extension = new $class();
                $container->registerExtension($extension);
                $extensions[] = $extension->getAlias();
            }
        }

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass(array_keys($container->getExtensions())));

        return $container;
    }
}
