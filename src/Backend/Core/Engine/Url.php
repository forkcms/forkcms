<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Config;
use Backend\Core\Engine\Base\Config as BackendBaseConfig;
use Backend\Core\Engine\Model as BackendModel;
use Common\Exception\RedirectException;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This class will handle the incoming URL.
 */
class Url extends KernelLoader
{
    /**
     * The Symfony request object
     *
     * @var Request
     */
    private $request;

    /**
     * The current action
     *
     * @var string
     */
    protected $action;

    /**
     * The current module
     *
     * @var string
     */
    protected $module;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->getContainer()->set('url', $this);

        $this->processQueryString();
    }

    /**
     * Get the domain
     *
     * @return string The current domain (without www.)
     */
    public function getDomain(): string
    {
        // replace
        return str_replace('www.', '', BackendModel::getRequest()->getHttpHost());
    }

    /**
     * Get the full querystring
     *
     * @return string
     */
    public function getQueryString(): string
    {
        return trim((string) BackendModel::getRequest()->getRequestUri(), '/');
    }

    private function getLanguageFromUrl(): string
    {
        if (!array_key_exists(BackendModel::getRequest()->attributes->get('_locale'), BackendLanguage::getWorkingLanguages())) {
            $url = $this->getBaseUrlForLanguage($this->getContainer()->getParameter('site.default_language'));
            $url .= '/' . BackendModel::getRequest()->attributes->get('module') . '/' . BackendModel::getRequest()->attributes->get('action');

            if (BackendModel::getRequest()->getQueryString() !== null) {
                $url .= '?' . BackendModel::getRequest()->getQueryString();
            }

            $this->redirect($url);
        }

        return BackendModel::getRequest()->attributes->get('_locale');
    }

    private function getModuleFromRequest(): string
    {
        $module = BackendModel::getRequest()->attributes->get('module');
        if (empty($module)) {
            return 'Dashboard';
        }

        return \SpoonFilter::toCamelCase($module);
    }

    private function getActionFromRequest(string $module, string $language): string
    {
        $action = BackendModel::getRequest()->attributes->get('action');
        if (!empty($action)) {
            return \SpoonFilter::toCamelCase($action);
        }

        return $this->getDefaultActionForModule($module, $language);
    }

    private function getDefaultActionForModule(string $module, string $language): string
    {
        // Check if we can load the config file
        $configClass = 'Backend\\Modules\\' . $module . '\\Config';
        if ($module === 'Core') {
            $configClass = Config::class;
        }

        if (!class_exists($configClass)) {
            if (BackendModel::getContainer()->getParameter('kernel.debug')) {
                throw new Exception('The config file for the module (' . $module . ') can\'t be found.');
            }

            $this->redirectWithQueryString(
                $language,
                '/error?type=action-not-allowed',
                Response::HTTP_TEMPORARY_REDIRECT
            );
        }

        /** @var BackendBaseConfig $config */
        $config = new $configClass($this->getKernel(), $module);

        return $config->getDefaultAction() ?? 'Index';
    }

    private function processQueryString(): void
    {
        if (BackendModel::getRequest()->attributes->get('_route') === 'backend_ajax') {
            $this->processAjaxRequest();

            return;
        }

        $language = $this->getLanguageFromUrl();
        $module = $this->getModuleFromRequest();
        $action = $this->getActionFromRequest($module, $language);

        $this->processRegularRequest($module, $action, $language);
    }

    private function splitUpForkData(array $forkData): array
    {
        $language = $forkData['language'] ?? '';

        if ($language === '') {
            $language = $this->getContainer()->getParameter('site.default_language');
        }

        return [
            $forkData['module'] ?? '',
            $forkData['action'] ?? '',
            $language,
        ];
    }

    private function getForkData(): array
    {
        if (BackendModel::getRequest()->request->has('fork')) {
            return $this->splitUpForkData((array) BackendModel::getRequest()->request->get('fork'));
        }

        if (BackendModel::getRequest()->query->has('fork')) {
            return $this->splitUpForkData((array) BackendModel::getRequest()->query->get('fork'));
        }

        return $this->splitUpForkData(BackendModel::getRequest()->query->all());
    }

    private function processAjaxRequest(): void
    {
        [$module, $action, $language] = $this->getForkData();

        $this->setAction($action, $module);
        BackendLanguage::setWorkingLanguage($language);
    }

    private function processRegularRequest(string $module, string $action, string $language): void
    {
        // the person isn't logged in? or the module doesn't require authentication
        if (!Authentication::isLoggedIn() && !Authentication::isAllowedModule($module)) {
            $this->redirectWithQueryString($language, '/authentication');
        }

        if (Authentication::isLoggedIn() && !Authentication::isAllowedModule($module)) {
            if ($module === 'Dashboard') {
                $this->redirectToFistAvailableLink(
                    $language,
                    $this->getContainer()->get('cache.backend_navigation')->get()
                );
            }

            $this->redirectWithQueryString(
                $language,
                '/error?type=module-not-allowed',
                Response::HTTP_TEMPORARY_REDIRECT
            );
        }

        if (!Authentication::isAllowedAction($action, $module)) {
            $this->redirectWithQueryString(
                $language,
                '/error?type=action-not-allowed',
                Response::HTTP_TEMPORARY_REDIRECT
            );
        }

        // set the working language, this is not the interface language
        BackendLanguage::setWorkingLanguage($language);

        $this->setLocale();
        $this->setModule($module);
        $this->setAction($action);
    }

    private function setLocale(): void
    {
        $defaultLocale = $this->get('fork.settings')->get('Core', 'default_interface_language');
        $locale = $this->getInterfaceLanguage();
        $possibleLocale = array_keys(BackendLanguage::getInterfaceLanguages());

        // set the default if the detected interface language is not enabled
        if (!in_array($locale, $possibleLocale, true)) {
            $locale = $defaultLocale;
        }

        BackendLanguage::setLocale($locale);
    }

    private function getInterfaceLanguage(): string
    {
        $default = $this->get('fork.settings')->get('Core', 'default_interface_language');

        if (Authentication::getUser()->isAuthenticated()) {
            return Authentication::getUser()->getSetting('interface_language', $default);
        }

        if ($this->getContainer()->get('fork.cookie')->has('interface_language')) {
            // no authenticated user, but available from a cookie
            return $this->getContainer()->get('fork.cookie')->get('interface_language');
        }

        return $default;
    }

    private function getBaseUrlForLanguage(string $language): string
    {
        return '/' . NAMED_APPLICATION . '/' . $language;
    }

    private function redirectWithQueryString(string $language, string $url, int $code = Response::HTTP_FOUND): void
    {
        // add a / at the start if needed
        if (mb_strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }

        $this->redirect($this->getBaseUrlForLanguage($language) . $this->addQueryStringToUrl($url), $code);
    }

    private function addQueryStringToUrl(string $url): string
    {
        $queryString = 'querystring=' . rawurlencode('/' . $this->getQueryString());

        if (mb_strpos($url, '?') !== false) {
            return $url . '&' . $queryString;
        }

        return $url . '?' . $queryString;
    }

    private function redirectToFistAvailableLink(string $language, array $navigation): void
    {
        foreach ($navigation as $navigationItem) {
            list($module, $action) = explode('/', $navigationItem['url']);
            $module = \SpoonFilter::toCamelCase($module);
            $action = \SpoonFilter::toCamelCase($action);

            if (Authentication::isAllowedModule($module) && Authentication::isAllowedAction($action, $module)) {
                $this->redirect(
                    $this->getBaseUrlForLanguage($language) . '/' . $navigationItem['url'],
                    Response::HTTP_TEMPORARY_REDIRECT
                );
            }

            if (array_key_exists('children', $navigationItem)) {
                $this->redirectToFistAvailableLink($language, $navigationItem['children']);
            }
        }
    }

    /**
     * Redirect to a given URL
     *
     * @param string $url The URL to redirect to.
     * @param int $code The redirect code, default is 302 which means this is a temporary redirect.
     *
     * @throws RedirectException
     */
    public function redirect(string $url, int $code = Response::HTTP_FOUND): void
    {
        throw new RedirectException('Redirect', new RedirectResponse($url, $code));
    }

    /**
     * Helper method to create a redirect to the error page of the backend
     *
     * @param string $type
     * @param int $code
     */
    public function redirectToErrorPage(string $type, int $code = Response::HTTP_BAD_REQUEST): void
    {
        $errorUrl = '/' . NAMED_APPLICATION . '/' . BackendModel::getRequest()->getLocale()
                    . '/error?type=' . $type;

        $this->get('url')->redirect($errorUrl, $code);
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    private function setAction(string $action, string $module = null): void
    {
        // set module
        if ($module !== null) {
            $this->setModule($module);
        }

        // check if module is set
        if ($this->getModule() === null) {
            throw new Exception('Module has not yet been set.');
        }

        // is this action allowed?
        if (!Authentication::isAllowedAction($action, $this->getModule())) {
            // set correct headers
            header('HTTP/1.1 403 Forbidden');

            // throw exception
            throw new Exception('Action not allowed.');
        }

        // set property
        $this->action = \SpoonFilter::toCamelCase($action);
    }

    private function setModule(string $module): void
    {
        // is this module allowed?
        if (!Authentication::isAllowedModule($module)) {
            // set correct headers
            header('HTTP/1.1 403 Forbidden');

            // throw exception
            throw new Exception('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }
}
