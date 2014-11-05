<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

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
     * {@inheritdoc}
     *
     * @api
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (false === $this->booted) {
            $this->boot();

            // define Fork constants
            $this->defineForkConstants();
        }

        return $this->getHttpKernel()->handle($request, $type, $catch);
    }

    /**
     * This will disappear in time in favour of container-driven parameters.
     * @deprecated
     */
    protected function defineForkConstants()
    {
        $container = $this->getContainer();

        Spoon::setDebug($container->getParameter('kernel.debug'));
        Spoon::setDebugMessage($container->getParameter('fork.debug_email'));
        Spoon::setDebugMessage($container->getParameter('fork.debug_message'));
        Spoon::setCharset($container->getParameter('kernel.charset'));

        /**
         * @deprecated SPOON_* constants are deprecated in favor of Spoon::set*().
         * Will be removed in the next major release.
         */
        if (!defined('SPOON_DEBUG')) {
            define('SPOON_DEBUG', $container->getParameter('kernel.debug'));
            define('SPOON_DEBUG_EMAIL', $container->getParameter('fork.debug_email'));
            define('SPOON_DEBUG_MESSAGE', $container->getParameter('fork.debug_message'));
            define('SPOON_CHARSET', $container->getParameter('kernel.charset'));
        }

        if (!defined('PATH_WWW')) {
            define('PATH_WWW', $container->getParameter('site.path_www'));
            define('PATH_LIBRARY', $container->getParameter('site.path_library'));
        }

        define('SITE_DEFAULT_LANGUAGE', $container->getParameter('site.default_language'));
        define('SITE_DEFAULT_TITLE', $container->getParameter('site.default_title'));
        define('SITE_MULTILANGUAGE', $container->getParameter('site.multilanguage'));
        define('SITE_DOMAIN', $container->getParameter('site.domain'));
        define('SITE_PROTOCOL', $container->getParameter('site.protocol'));
        define('SITE_URL', SITE_PROTOCOL . '://' . SITE_DOMAIN);

        define('FORK_VERSION', $container->getParameter('fork.version'));

        define('ACTION_GROUP_TAG', $container->getParameter('action.group_tag'));
        define('ACTION_RIGHTS_LEVEL', $container->getParameter('action.rights_level'));
    }
}
