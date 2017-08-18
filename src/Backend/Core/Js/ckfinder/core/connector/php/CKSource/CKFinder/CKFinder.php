<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder;

use CKSource\CKFinder\Acl\Acl;
use CKSource\CKFinder\Acl\User\SessionRoleContext;
use CKSource\CKFinder\Authentication\CallableAuthentication;
use CKSource\CKFinder\Backend\BackendFactory;
use CKSource\CKFinder\Cache\CacheManager;
use CKSource\CKFinder\Cache\Adapter\BackendAdapter;
use CKSource\CKFinder\Event\AfterCommandEvent;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Exception\CKFinderException;
use CKSource\CKFinder\Exception\InvalidCsrfTokenException;
use CKSource\CKFinder\Exception\InvalidPluginException;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Operation\OperationManager;
use CKSource\CKFinder\Plugin\PluginInterface;
use CKSource\CKFinder\Request\Transformer\JsonTransformer;
use CKSource\CKFinder\ResourceType\ResourceTypeFactory;
use CKSource\CKFinder\Response\JsonResponse;
use CKSource\CKFinder\ResizedImage\ResizedImageRepository;
use CKSource\CKFinder\Security\Csrf\DoubleSubmitCookieTokenValidator;
use CKSource\CKFinder\Thumbnail\ThumbnailRepository;
use League\Flysystem\Adapter\Local as LocalFSAdapter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The main CKFinder class.
 *
 * It is based on <a href="http://pimple.sensiolabs.org/">Pimple</a>
 * so it also serves as a dependency injection container.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
class CKFinder extends Container implements HttpKernelInterface
{
    const VERSION = '3.3.0';

    const COMMANDS_NAMESPACE = 'CKSource\\CKFinder\\Command\\';
    const PLUGINS_NAMESPACE = 'CKSource\\CKFinder\\Plugin\\';

    const CHARS = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    protected $plugins = array();

    protected $booted = false;

    /**
     * Constructor.
     *
     * @param array|string $config an array containing configuration options or a path
     *                             to the configuration file.
     *
     * @see config.php
     */
    public function __construct($config)
    {
        parent::__construct();

        $app = $this;

        $this['config'] = function () use ($config) {
            return new Config($config);
        };

        $this['authentication'] = function () use ($app) {
            $config = $app['config'];

            return new CallableAuthentication($config->get('authentication'));
        };

        $this['exception_handler'] = function () use ($app) {
            return new ExceptionHandler($app['translator'], $app['debug'], $app['logger']);
        };

        $this['dispatcher'] = function () use ($app) {
            $eventDispatcher = new EventDispatcher();

            $eventDispatcher->addListener(KernelEvents::VIEW, array($this, 'createResponse'), -512);
            $eventDispatcher->addListener(KernelEvents::RESPONSE, array($this, 'afterCommand'), -512);

            $eventDispatcher->addSubscriber($app['exception_handler']);

            return $eventDispatcher;
        };

        $this['resolver'] = function () use ($app) {
            $commandResolver = new CommandResolver($app);
            $commandResolver->setCommandsNamespace(CKFinder::COMMANDS_NAMESPACE);
            $commandResolver->setPluginsNamespace(CKFinder::PLUGINS_NAMESPACE);

            return $commandResolver;
        };

        $this['request_stack'] = function () {
            return new RequestStack();
        };

        $this['request_transformer'] = function () {
            return new JsonTransformer();
        };

        $this['working_folder'] = function () use ($app) {
            $workingFolder = new WorkingFolder($app);

            $this['dispatcher']->addSubscriber($workingFolder);

            return $workingFolder;
        };

        $this['operation'] = function () use ($app) {
            return new OperationManager($app);
        };

        $this['kernel'] = function () use ($app) {
            return new HttpKernel($app['dispatcher'], $app['resolver'], $app['request_stack']);
        };

        $this['acl'] = function () use ($app) {
            $config = $app['config'];

            $roleContext = new SessionRoleContext($config->get('roleSessionVar'));

            $acl = new Acl($roleContext);
            $acl->setRules($config->get('accessControl'));

            return $acl;
        };

        $this['backend_factory'] = function () use ($app) {
            return new BackendFactory($app);
        };

        $this['resource_type_factory'] = function () use ($app) {
            return new ResourceTypeFactory($app);
        };

        $this['thumbnail_repository'] = function () use ($app) {
            return new ThumbnailRepository($app);
        };

        $this['resized_image_repository'] = function () use ($app) {
            return new ResizedImageRepository($app);
        };

        $this['cache'] = function () use ($app) {
            $cacheBackend = $app['backend_factory']->getPrivateDirBackend('cache');
            $cacheDir = $app['config']->getPrivateDirPath('cache') . '/data';

            return new CacheManager(new BackendAdapter($cacheBackend, $cacheDir));
        };

        $this['translator'] = function () {
            return new Translator();
        };

        $this['debug'] = $app['config']->get('debug');

        $this['logger'] = function () use ($app) {
            $logger = new Logger('CKFinder');

            if ($app['config']->isDebugLoggerEnabled('firephp')) {
                $logger->pushHandler(new FirePHPHandler());
            }

            if ($app['config']->isDebugLoggerEnabled('error_log')) {
                $logger->pushHandler(new ErrorLogHandler());
            }

            return $logger;
        };

        if ($app['config']->get('csrfProtection')) {
            $config = $app['config'];

            $this['csrf_token_validator'] = function () use ($config) {
                return new DoubleSubmitCookieTokenValidator();
            };
        }
    }

    /**
     * Checks authentication.
     */
    public function checkAuth()
    {
        /* @var $authentication \CKSource\CKFinder\Authentication\AuthenticationInterface */
        $authentication = $this['authentication'];

        if (!$authentication->authenticate()) {
            ini_set('display_errors', 0);
            throw new CKFinderException('CKFinder is disabled', Error::CONNECTOR_DISABLED);
        }
    }

    /**
     * Validates the CSRF token.
     *
     * @param Request $request
     *
     * @throws InvalidCsrfTokenException
     */
    public function checkCsrfToken(Request $request)
    {
        if ($request->getMethod() === Request::METHOD_GET) {
            return;
        }

        /* @var $csrfTokenValidator \CKSource\CKFinder\Security\Csrf\TokenValidatorInterface */
        $csrfTokenValidator = $this['csrf_token_validator'];

        if (!$csrfTokenValidator->validate($request)) {
            throw new InvalidCsrfTokenException();
        }
    }

    /**
     * Creates a response.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function createResponse(GetResponseForControllerResultEvent $event)
    {
        /* @var $dispatcher EventDispatcher */
        $dispatcher = $this['dispatcher'];

        $commandName = $event->getRequest()->get('command');
        $eventName = CKFinderEvent::CREATE_RESPONSE_PREFIX . lcfirst($commandName);
        $dispatcher->dispatch($eventName, $event);

        $controllerResult = $event->getControllerResult();
        $event->setResponse(JsonResponse::create($controllerResult));
    }

    /**
     * Fires `afterCommand` events.
     *
     * @param FilterResponseEvent $event
     *
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function afterCommand(FilterResponseEvent $event)
    {
        /* @var $dispatcher EventDispatcher */
        $dispatcher = $this['dispatcher'];

        $commandName = $event->getRequest()->get('command');
        $eventName = CKFinderEvent::AFTER_COMMAND_PREFIX . lcfirst($commandName);
        $afterCommandEvent = new AfterCommandEvent($this, $commandName, $event->getResponse());
        $dispatcher->dispatch($eventName, $afterCommandEvent);

        // #161 Clear any garbage from the output
        Response::closeOutputBuffers(0, false);

        $event->setResponse($afterCommandEvent->getResponse());
    }

    /**
     * Registers a listener for an event.
     *
     * @param string   $eventName event name
     * @param callable $listener  listener callable
     * @param int      $priority  priority
     */
    public function on($eventName, $listener, $priority = 0)
    {
        /* @var $dispatcher EventDispatcher */
        $dispatcher = $this['dispatcher'];

        $dispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Main method used to handle a request by CKFinder.
     *
     * @param Request $request request object
     */
    public function run(Request $request = null)
    {
        $request = null === $request ? Request::createFromGlobals() : $request;

        /* @var $kernel HttpKernel */
        $kernel = $this['kernel'];

        $response = $this->handle($request);
        $response->send();

        $kernel->terminate($request, $response);
    }

    /**
     * Returns the BackedFactory service.
     * 
     * @return BackendFactory
     */
    public function getBackendFactory()
    {
        return $this['backend_factory'];
    }

    /**
     * Returns the ACL service.
     *
     * @return Acl
     */
    public function getAcl()
    {
        return $this['acl'];
    }

    /**
     * Returns the current WorkingFolder object.
     *
     * @return WorkingFolder
     */
    public function getWorkingFolder()
    {
        return $this['working_folder'];
    }

    /**
     * Shorthand for debugging using the defined logger.
     *
     * @param string $message
     * @param array  $context
     */
    public function debug($message, array $context = array())
    {
        $logger = $this['logger'];

        if ($logger) {
            $logger->debug($message, $context);
        }
    }

    /**
     * Registers plugins defined in the configuration file.
     *
     * @throws \LogicException in case the plugin was not found or is invalid.
     */
    protected function registerPlugins()
    {
        $pluginsEntries = $this['config']->get('plugins');
        $pluginsDirectory = $this['config']->get('pluginsDirectory');

        foreach ($pluginsEntries as $pluginInfo) {
            if (is_array($pluginInfo)) {
                $pluginName = ucfirst($pluginInfo['name']);
                if (isset($pluginInfo['path'])) {
                    require_once $pluginInfo['path'];
                }
            } else {
                $pluginName = ucfirst($pluginInfo);
            }

            $pluginPath = Path::combine($pluginsDirectory, $pluginName, $pluginName . '.php');

            if (file_exists($pluginPath) && is_readable($pluginPath)) {
                require_once $pluginPath;
            }

            $pluginClassName = CKFinder::PLUGINS_NAMESPACE . $pluginName . '\\' . $pluginName;

            if (!class_exists($pluginClassName)) {
                throw new InvalidPluginException(sprintf('CKFinder plugin "%s" not found (%s)', $pluginName, $pluginClassName), array('pluginName' => $pluginName));
            }

            $pluginObject = new $pluginClassName($this);

            if ($pluginObject instanceof PluginInterface) {
                $this->registerPlugin($pluginObject);
            } else {
                throw new InvalidPluginException(sprintf('CKFinder plugin class must implement %sPluginInterface', CKFinder::PLUGINS_NAMESPACE), array('pluginName' => $pluginName));
            }
        }
    }

    /**
     * Registers the plugin.
     *
     * @param PluginInterface $plugin
     */
    public function registerPlugin(PluginInterface $plugin)
    {
        $plugin->setContainer($this);

        $pluginNameParts = explode('\\', get_class($plugin));
        $pluginName = end($pluginNameParts);

        $this['config']->extend($pluginName, $plugin->getDefaultConfig());

        if ($plugin instanceof EventSubscriberInterface) {
            $this['dispatcher']->addSubscriber($plugin);
        }

        $this->plugins[$pluginName] = $plugin;
    }

    /**
     * Returns an array containing all registered plugins.
     *
     * @return array array of PluginInterface-s
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Returns a plugin by the name.
     *
     * @param string $name plugin name
     *
     * @return null|PluginInterface
     *
     */
    public function getPlugin($name)
    {
        if (isset($this->plugins[$name])) {
            return $this->plugins[$name];
        }

        return null;
    }

    /**
     * Checks PHP requirements.
     *
     * @throws CKFinderException
     */
    protected function checkRequirements()
    {
        $errorMessage = 'The PHP installation does not meet the minimum system requirements for CKFinder. %s Please refer to CKFinder documentation for more details.';

        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            throw new CKFinderException(sprintf($errorMessage, 'Your PHP version is too old. CKFinder 3.x requires PHP 5.4+.'), Error::CUSTOM_ERROR);
        }

        $missingExtensions = array();

        if (!function_exists('gd_info')) {
            $missingExtensions[] = 'GD';
        }

        if (!function_exists('finfo_file')) {
            $missingExtensions[] = 'Fileinfo';
        }

        if (!empty($missingExtensions)) {
            throw new CKFinderException(sprintf($errorMessage, 'Missing PHP extensions: ' . implode(', ', $missingExtensions) . '.'), Error::CUSTOM_ERROR);
        }
    }

    /**
     * Prepares application environment before the Request is dispatched.
     *
     * @param Request $request
     *
     * @throws CKFinderException
     * @throws InvalidPluginException
     */
    public function boot(Request $request)
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;

        $config = $this['config'];

        $this->checkRequirements();

        if ($config->get('debug') && $config->isDebugLoggerEnabled('ckfinder_log')) {
            $this->registerStreamLogger();
        }

        $this->checkAuth();

        if ($config->get('csrfProtection')) {
            $this->checkCsrfToken($request);
        }

        $this->registerPlugins();

        $commandName = (string) $request->query->get('command');

        if ($config->get('sessionWriteClose') && $commandName !== 'Init' && session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    /**
     * Registers a stream handler for error logging.
     */
    public function registerStreamLogger()
    {
        $app = $this;

        /* @var $logsBackend \CKSource\CKFinder\Backend\Backend */
        $logsBackend = $app['backend_factory']->getPrivateDirBackend('logs');

        $adapter = $logsBackend->getBaseAdapter();

        if ($adapter instanceof LocalFSAdapter) {
            $logsDir = $app['config']->getPrivateDirPath('logs');

            $errorLogPath = Path::combine($logsDir, 'error.log');

            $logPath = $adapter->applyPathPrefix($errorLogPath);

            $app['logger']->pushHandler(new StreamHandler($logPath));
        }
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        /* @var $kernel HttpKernel */
        $kernel = $this['kernel'];

        /* @var $requestTransformer \CKSource\CKFinder\Request\Transformer\TransformerInterface */
        $requestTransformer = $this['request_transformer'];

        if ($requestTransformer) {
            $request = $requestTransformer->transform($request);
        }

        // Handle early exceptions
        if (!$this->booted) {
            try {
                $this->boot($request);
            } catch (\Exception $e) {
                $this['request_stack']->push($request);
                $kernel->terminateWithException($e);
                exit;
            }
        }

        return $kernel->handle($request, $type, $catch);
    }

    /**
     * Returns the current request object.
     *
     * @return Request
     */
    public function getRequest()
    {
        /* @var $requestStack RequestStack */
        $requestStack = $this['request_stack'];

        return $requestStack->getCurrentRequest();
    }

    /**
     * Returns the resized image repository.
     *
     * @return ResizedImageRepository
     */
    public function getResizedImageRepository()
    {
        return $this['resized_image_repository'];
    }

    /**
     * Returns the connector URL based on the current request.
     *
     * @param bool|true $full if set to `true`, the returned URL contains the scheme and host.
     *
     * @return string
     */
    public function getConnectorUrl($full = true)
    {
        $request = $this->getRequest();

        return ($full ? $request->getSchemeAndHttpHost() : '').$request->getBaseUrl();
    }
}
