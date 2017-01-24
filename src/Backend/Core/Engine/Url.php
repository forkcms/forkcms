<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Config as BackendBaseConfig;
use Backend\Core\Engine\Model as BackendModel;
use Common\Cookie as CommonCookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This class will handle the incoming URL.
 */
class Url extends Base\Object
{
    /**
     * The Symfony request object
     *
     * @var Request
     */
    private $request;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        // add to registry
        $this->getContainer()->set('url', $this);

        // fetch the request object from the container
        $this->request = $this->get('request');

        $this->processQueryString();
    }

    /**
     * Get the domain
     *
     * @return string The current domain (without www.)
     */
    public function getDomain()
    {
        // replace
        return str_replace('www.', '', $this->request->getHttpHost());
    }

    /**
     * Get the full querystring
     *
     * @return string
     */
    public function getQueryString()
    {
        return trim((string) $this->request->getRequestUri(), '/');
    }

    /**
     * Process the querystring
     */
    private function processQueryString()
    {
        // store the querystring local, so we don't alter it.
        $queryString = $this->getQueryString();

        // find the position of ? (which separates real URL and GET-parameters)
        $positionQuestionMark = mb_strpos($queryString, '?');

        // separate the GET-chunk from the parameters
        $getParameters = '';
        if ($positionQuestionMark === false) {
            $processedQueryString = $queryString;
        } else {
            $processedQueryString = mb_substr($queryString, 0, $positionQuestionMark);
            $getParameters = mb_substr($queryString, $positionQuestionMark);
        }

        // split into chunks, a Backend URL will always look like /<lang>/<module>/<action>(?GET)
        $chunks = (array) explode('/', trim($processedQueryString, '/'));

        // check if this is a request for a AJAX-file
        $isAJAX = (isset($chunks[1]) && $chunks[1] == 'ajax');

        // get the language, this will always be in front
        $language = '';
        if (isset($chunks[1]) && $chunks[1] != '') {
            $language = \SpoonFilter::getValue(
                $chunks[1],
                array_keys(BackendLanguage::getWorkingLanguages()),
                ''
            );
        }

        // no language provided?
        if ($language == '' && !$isAJAX) {
            // remove first element
            array_shift($chunks);

            // redirect to login
            $this->redirect(
                '/' . NAMED_APPLICATION . '/' . SITE_DEFAULT_LANGUAGE . (empty($chunks) ? '' : '/') . implode('/', $chunks) . $getParameters
            );
        }

        // get the module, null will be the default
        $module = (isset($chunks[2]) && $chunks[2] != '') ? $chunks[2] : 'Dashboard';
        $module = \SpoonFilter::toCamelCase($module);

        // get the requested action, if it is passed
        if (isset($chunks[3]) && $chunks[3] != '') {
            $action = \SpoonFilter::toCamelCase($chunks[3]);
        } elseif (!$isAJAX) {
            // Check if we can load the config file
            $configClass = 'Backend\\Modules\\' . $module . '\\Config';
            if ($module == 'Core') {
                $configClass = 'Backend\\Core\\Config';
            }

            try {
                // when loading a backend url for a module that doesn't exist, without
                // providing an action, a FatalErrorException occurs, because the config
                // class we're trying to load doesn't exist. Let's just throw instead,
                // and catch it immediately.
                if (!class_exists($configClass)) {
                    throw new Exception('The config class does not exist');
                }

                /** @var BackendBaseConfig $config */
                $config = new $configClass($this->getKernel(), $module);

                // set action
                $action = ($config->getDefaultAction() !== null) ? $config->getDefaultAction() : 'Index';
            } catch (Exception $ex) {
                if (BackendModel::getContainer()->getParameter('kernel.debug')) {
                    throw new Exception('The config file for the module (' . $module . ') can\'t be found.');
                } else {
                    // @todo    don't use redirects for error, we should have something like an invoke method.

                    // build the url
                    $errorUrl = '/' . NAMED_APPLICATION . '/' . $language . '/error?type=action-not-allowed';

                    // add the querystring, it will be processed by the error-handler
                    $errorUrl .= '&querystring=' . rawurlencode('/' . $this->getQueryString());

                    // redirect to the error page
                    $this->redirect($errorUrl, 307);
                }
            }
        }

        // AJAX parameters are passed via GET or POST
        if ($isAJAX) {
            $module = (isset($_GET['fork']['module'])) ? $_GET['fork']['module'] : '';
            $action = (isset($_GET['fork']['action'])) ? $_GET['fork']['action'] : '';
            $language = (isset($_GET['fork']['language'])) ? $_GET['fork']['language'] : SITE_DEFAULT_LANGUAGE;
            $module = (isset($_POST['fork']['module'])) ? $_POST['fork']['module'] : $module;
            $action = (isset($_POST['fork']['action'])) ? $_POST['fork']['action'] : $action;
            $language = (isset($_POST['fork']['language'])) ? $_POST['fork']['language'] : $language;

            $this->setModule($module);
            $this->setAction($action);
            BackendLanguage::setWorkingLanguage($language);
        } else {
            $this->processRegularRequest($module, $action, $language);
        }
    }

    /**
     * Process a regular request
     *
     * @param string $module The requested module.
     * @param string $action The requested action.
     * @param string $language The requested language.
     */
    private function processRegularRequest($module, $action, $language)
    {
        // the person isn't logged in? or the module doesn't require authentication
        if (!Authentication::isLoggedIn() && !Authentication::isAllowedModule($module)) {
            // redirect to login
            $this->redirect(
                '/' . NAMED_APPLICATION . '/' . $language . '/authentication?querystring=' . rawurlencode(
                    '/' . $this->getQueryString()
                )
            );
        } elseif (Authentication::isLoggedIn() && !Authentication::isAllowedModule($module)) {
            // the person is logged in, but doesn't have access to our action
            // if the module is the dashboard redirect to the first allowed module
            if ($module == 'Dashboard') {
                // require navigation-file
                require_once Navigation::getCacheDirectory() . 'navigation.php';

                // loop the navigation to find the first allowed module
                foreach ($navigation as $value) {
                    // split up chunks
                    list($module, $action) = explode('/', $value['url']);

                    // user allowed?
                    if (Authentication::isAllowedModule($module)) {
                        // redirect to the page
                        $this->redirect('/' . NAMED_APPLICATION . '/' . $language . '/' . $value['url']);
                    } else {
                        if (array_key_exists('children', $value)) {
                            foreach ($value['children'] as $subItem) {
                                // split up chunks
                                list($module, $action) = explode('/', $subItem['url']);

                                // user allowed?
                                if (Authentication::isAllowedModule($module)) {
                                    $finder = new Finder();
                                    $files = $finder->files()->name('*.php')->in(BACKEND_MODULES_PATH . '/' . \SpoonFilter::toCamelCase($module) . '/Actions');
                                    foreach ($files as $file) {
                                        $moduleAction = mb_substr($file->getFilename(), 0, -4);
                                        if (Authentication::isAllowedAction($moduleAction, $module)) {
                                            $this->redirect('/' . NAMED_APPLICATION . '/' . $language . '/' .
                                                $module . '/' . $moduleAction);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // the user doesn't have access, redirect to error page
            $this->redirect(
                '/' . NAMED_APPLICATION . '/' . $language .
                '/error?type=module-not-allowed&querystring=' . rawurlencode('/' . $this->getQueryString()),
                307
            );
        } elseif (!Authentication::isAllowedAction($action, $module)) {
            // the user hasn't access, redirect to error page
            $this->redirect(
                '/' . NAMED_APPLICATION . '/' . $language .
                '/error?type=action-not-allowed&querystring=' . rawurlencode('/' . $this->getQueryString()),
                307
            );
        } else {
            // set the working language, this is not the interface language
            BackendLanguage::setWorkingLanguage($language);

            $this->setLocale();
            $this->setModule($module);
            $this->setAction($action);
        }
    }

    /**
     * Set the locale
     */
    private function setLocale()
    {
        $default = $this->get('fork.settings')->get('Core', 'default_interface_language');
        $locale = $default;
        $possibleLocale = array_keys(BackendLanguage::getInterfaceLanguages());

        // is the user authenticated
        if (Authentication::getUser()->isAuthenticated()) {
            $locale = Authentication::getUser()->getSetting('interface_language', $default);
        } elseif (CommonCookie::exists('interface_language')) {
            // no authenticated user, but available from a cookie
            $locale = CommonCookie::get('interface_language');
        }

        // validate if the requested locale is possible
        if (!in_array($locale, $possibleLocale)) {
            $locale = $default;
        }

        BackendLanguage::setLocale($locale);
    }
}
