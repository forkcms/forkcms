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
use Common\Cookie as CommonCookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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

        $this->getContainer()->set('url', $this);
        $this->request = $this->get('request');

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
        return str_replace('www.', '', $this->request->getHttpHost());
    }

    /**
     * Get the full querystring
     *
     * @return string
     */
    public function getQueryString(): string
    {
        return trim((string) $this->request->getRequestUri(), '/');
    }

    private function getLanguageFromUrl(): string
    {
        if (!array_key_exists($this->request->get('_locale'), BackendLanguage::getWorkingLanguages())) {
            $url = $this->getBaseUrlForLanguage($this->getContainer()->getParameter('site.default_language'));
            $url .= '/' . $this->request->get('module') . '/' . $this->request->get('action');

            if ($this->request->getQueryString() !== null) {
                $url .= '?' . $this->request->getQueryString();
            }

            $this->redirect($url);
        }

        return $this->request->get('_locale');
    }

    private function getModuleFromRequest(): string
    {
        $module = $this->request->get('module');
        if (empty($module)) {
            return 'Dashboard';
        }

        return \SpoonFilter::toCamelCase($module);
    }

    private function getActionFromRequest(string $module, string $language): string
    {
        $action = $this->request->get('action');
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
        if ($this->request->get('_route') === 'backend_ajax') {
            $this->processAjaxRequest();

            return;
        }

        $language = $this->getLanguageFromUrl();
        $module = $this->getModuleFromRequest();
        $action = $this->getActionFromRequest($module, $language);

        $this->processRegularRequest($module, $action, $language);
    }

    private function getForkData(): array
    {
        $request = $this->getContainer()->get('request');

        if ($request->request->has('fork')) {
            return (array) $request->request->get('fork');
        }

        if ($request->query->has('fork')) {
            return (array) $request->query->get('fork');
        }

        return (array) $request->query->all();
    }

    private function processAjaxRequest(): void
    {
        $forkData = $this->getForkData();
        $language = $forkData['language'] ?? $this->getContainer()->getParameter('site.default_language');

        $this->setAction($forkData['action'] ?? '', $forkData['module'] ?? '');
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

        if (CommonCookie::exists('interface_language')) {
            // no authenticated user, but available from a cookie
            return CommonCookie::get('interface_language');
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
}
