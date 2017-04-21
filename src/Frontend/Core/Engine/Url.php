<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Exception\RedirectException;
use ForkCMS\App\KernelLoader;
use Frontend\Core\Language\Language;
use SpoonSession;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Common\Cookie as CommonCookie;

/**
 * This class will handle the incoming URL.
 */
class Url extends KernelLoader
{
    /**
     * The pages
     *
     * @var array
     */
    private $pages = [];

    /**
     * The Symfony request object
     *
     * @var Request
     */
    private $request;

    /**
     * The parameters
     *
     * @var array
     */
    private $parameters = [];

    /**
     * @param KernelInterface $kernel
     *
     * @throws RedirectException
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        // add ourself to the reference so other classes can retrieve us
        $this->getContainer()->set('url', $this);

        // fetch the request object from the container
        $this->request = $this->get('request');

        // if there is a trailing slash we permanent redirect to the page without slash
        if (mb_strlen($this->request->getRequestUri()) !== 1 &&
            mb_substr($this->request->getRequestUri(), -1) === '/'
        ) {
            throw new RedirectException(
                'Redirect',
                new RedirectResponse(
                    mb_substr($this->request->getRequestUri(), 0, -1),
                    301
                )
            );
        }

        // set query-string and parameters for later use
        $this->parameters = $this->request->query->all();

        // process URL
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
     * Get a page specified by the given index
     *
     * @param int $index The index (0-based).
     *
     * @return mixed
     */
    public function getPage(int $index)
    {
        if (isset($this->pages[$index])) {
            return $this->pages[$index];
        }
    }

    /**
     * Return all the pages
     *
     * @return array
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * Get a parameter specified by the given index
     * The function will return null if the key is not available
     * By default we will cast the return value into a string, if you want
     * something else specify it by passing the wanted type.
     *
     * @param mixed $index The index of the parameter.
     * @param string $type The return type, possible values are:
     *                             bool, boolean, int, integer, float, double, string, array.
     * @param mixed $defaultValue The value that should be returned if the key is not available.
     *
     * @return mixed
     */
    public function getParameter($index, string $type = 'string', $defaultValue = null)
    {
        // does the index exists and isn't this parameter empty
        if ($this->hasParameter($index)) {
            return \SpoonFilter::getValue(
                $this->parameters[$index],
                null,
                null,
                $type
            );
        }

        // fallback
        return $defaultValue;
    }

    /**
     * Return all the parameters
     *
     * @param bool $includeGET Should the GET-parameters be included?
     *
     * @return array
     */
    public function getParameters($includeGET = true): array
    {
        return $includeGET ? $this->parameters : array_diff_assoc($this->parameters, $this->request->query->all());
    }

    /**
     * Get the query string
     *
     * @return string
     */
    public function getQueryString(): string
    {
        return rtrim($this->request->getRequestUri(), '/');
    }

    /**
     * Check if a certain ($_GET) parameter exists
     *
     * @param  mixed $index The index of the parameter.
     *
     * @return bool
     */
    public function hasParameter($index): bool
    {
        return isset($this->parameters[$index]) && $this->parameters[$index] !== '';
    }

    /**
     * Process the query string
     */
    private function processQueryString()
    {
        // store the query string local, so we don't alter it.
        $queryString = trim($this->request->getPathInfo(), '/');

        $hasMultiLanguages = $this->getContainer()->getParameter('site.multilanguage');
        $language = $this->determineLanguage($queryString);

        // define the language
        defined('FRONTEND_LANGUAGE') || define('FRONTEND_LANGUAGE', $language);
        defined('LANGUAGE') || define('LANGUAGE', $language);

        // sets the locale file
        Language::setLocale($language);

        // remove language from query string
        if ($hasMultiLanguages) {
            $queryString = trim(mb_substr($queryString, mb_strlen($language)), '/');
        }

        $url = $this->determineUrl($queryString, $language);

        // currently not in the homepage
        if ($url !== '') {
            $this->setPages(explode('/', $url));
        }

        $parameters = $this->extractParametersFromQueryString($queryString, $url);

        // pageId, parentId & depth
        $pageId = Navigation::getPageId(implode('/', $this->getPages()));
        $pageInfo = Navigation::getPageInfo($pageId);

        // invalid page, or parameters but no extra
        if ($pageInfo === false || (!empty($parameters) && !$pageInfo['has_extra'])) {
            // get 404 URL
            $url = Navigation::getURL(404);

            // remove language
            if ($hasMultiLanguages) {
                $url = str_replace('/' . $language, '', $url);
            }

            // remove the first slash
            $url = trim($url, '/');

            // currently not in the homepage
            if ($url !== '') {
                $this->setPages(explode('/', $url));
            }
        }

        if ($pageInfo !== false) {
            $this->handleRedirects($pageInfo);
        }
    }

    /**
     * @param string $queryString
     * @param string $url
     *
     * @return array
     */
    private function extractParametersFromQueryString(string $queryString, string $url): array
    {
        // set parameters
        $parameters = trim(mb_substr($queryString, mb_strlen($url)), '/');

        if (empty($parameters)) {
            return [];
        }

        // parameters will be separated by /
        $parameters = explode('/', $parameters);

        // set parameters
        $this->setParameters($parameters);

        return $parameters;
    }

    /**
     * @param string $queryString
     *
     * @return string
     */
    private function determineLanguage(string $queryString): string
    {
        if ($this->getContainer()->getParameter('site.multilanguage')) {
            return $this->get('fork.settings')->get('Core', 'default_language', SITE_DEFAULT_LANGUAGE);
        }

        // get possible languages
        $possibleLanguages = (array) Language::getActiveLanguages();
        $redirectLanguages = (array) Language::getRedirectLanguages();

        // split into chunks
        $chunks = (array) explode('/', $queryString);

        // the language is present in the URL
        if (isset($chunks[0]) && in_array($chunks[0], $possibleLanguages)) {
            // define language
            $language = (string) $chunks[0];
            $this->setLanguageCookie($language);

            SpoonSession::set('frontend_language', $language);

            return $language;
        }

        if (CommonCookie::exists('frontend_language')
            && in_array(CommonCookie::get('frontend_language'), $redirectLanguages)
        ) {
            $this->redirectToLanguage((string) CommonCookie::get('frontend_language'));
        }

        // default browser language
        $language = Language::getBrowserLanguage();
        $this->setLanguageCookie($language);
        $this->redirectToLanguage($language);
    }

    /**
     * @param string $language
     */
    private function setLanguageCookie(string $language)
    {
        try {
            CommonCookie::set('frontend_language', $language);
        } catch (\SpoonCookieException $e) {
            // settings cookies isn't allowed, because this isn't a real problem we ignore the exception
        }
    }

    /**
     * @param string $language
     *
     * @throws RedirectException
     */
    private function redirectToLanguage(string $language)
    {
        // trim the first / from the query string to prevent double slashes
        $url = rtrim('/' . $language . '/' . trim($this->getQueryString(), '/'), '/');
        // when we are just adding the language to the domain, it's a temporary redirect because
        // Safari keeps the 301 in cache, so the cookie to switch language doesn't work any more
        $redirectCode = ($url === '/' . $language ? 302 : 301);

        // set header & redirect
        throw new RedirectException(
            'Redirect',
            new RedirectResponse($url, $redirectCode)
        );
    }

    /**
     * @param array $pageInfo
     *
     * @throws RedirectException
     */
    private function handleRedirects(array $pageInfo)
    {
        // is this an internal redirect?
        if (isset($pageInfo['redirect_page_id']) && $pageInfo['redirect_page_id'] !== '') {
            // get url for item
            $newPageURL = Navigation::getURL((int) $pageInfo['redirect_page_id']);
            $errorURL = Navigation::getURL(404);

            // not an error?
            if ($newPageURL !== $errorURL) {
                // redirect
                throw new RedirectException(
                    'Redirect',
                    new RedirectResponse(
                        $newPageURL,
                        $pageInfo['redirect_code']
                    )
                );
            }
        }

        // is this an external redirect?
        if (isset($pageInfo['redirect_url']) && $pageInfo['redirect_url'] !== '') {
            // redirect
            throw new RedirectException(
                'Redirect',
                new RedirectResponse(
                    $pageInfo['redirect_url'],
                    $pageInfo['redirect_code']
                )
            );
        }
    }

    /**
     * Set the pages
     *
     * @param array $pages An array of all the pages to set.
     */
    private function setPages(array $pages = [])
    {
        $this->pages = $pages;
    }

    /**
     * Set the parameters
     *
     * @param array $parameters An array of all the parameters to set.
     */
    private function setParameters(array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

    /**
     * @param string $queryString
     * @param string $language
     *
     * @return string
     */
    private function determineUrl(string $queryString, string $language): string
    {
        // list of pageIds & their full URL
        $keys = Navigation::getKeys();
        $chunks = (array) explode('/', $queryString);

        // rebuild our URL, but without the language parameter. (it's tripped earlier)
        $url = implode('/', $chunks);

        // loop until we find the URL in the list of pages
        while (!in_array($url, $keys)) {
            // remove the last chunk
            array_pop($chunks);

            // redefine the URL
            $url = implode('/', $chunks);
        }

        // if it's the homepage AND parameters were given (not allowed!)
        if ($url === '' && $queryString !== '') {
            // get 404 URL
            $url = Navigation::getURL(404);

            // remove language
            if ($this->getContainer()->getParameter('site.multilanguage')) {
                $url = str_replace('/' . $language, '', $url);
            }
        }

        // set pages
        $url = trim($url, '/');

        return $url;
    }
}
