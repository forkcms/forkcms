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
use SpoonFilter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * The parameters
     *
     * @var array
     */
    private $parameters = [];

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        // add ourself to the reference so other classes can retrieve us
        $this->getContainer()->set('url', $this);

        // if there is a trailing slash we permanent redirect to the page without slash
        if (mb_strlen(Model::getRequest()->getRequestUri()) !== 1 &&
            mb_substr(Model::getRequest()->getRequestUri(), -1) === '/'
        ) {
            throw new RedirectException(
                'Redirect',
                new RedirectResponse(
                    mb_substr(Model::getRequest()->getRequestUri(), 0, -1),
                    301
                )
            );
        }

        // set query-string and parameters for later use
        $this->parameters = Model::getRequest()->query->all();

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
        return str_replace('www.', '', Model::getRequest()->getHttpHost());
    }

    /**
     * Get a page specified by the given index
     *
     * @param int $index The index (0-based).
     *
     * @return string|null
     */
    public function getPage(int $index): ?string
    {
        return $this->pages[$index] ?? null;
    }

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
            return SpoonFilter::getValue(
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
     * @param bool $includeGet Should the GET-parameters be included?
     *
     * @return array
     */
    public function getParameters(bool $includeGet = true): array
    {
        return $includeGet ? $this->parameters : array_diff_assoc($this->parameters, Model::getRequest()->query->all());
    }

    public function getQueryString(): string
    {
        return rtrim(Model::getRequest()->getRequestUri(), '/');
    }

    /**
     * Check if a certain ($_GET) parameter exists
     *
     * @param mixed $index The index of the parameter.
     *
     * @return bool
     */
    public function hasParameter($index): bool
    {
        return isset($this->parameters[$index]) && $this->parameters[$index] !== '';
    }

    private function processQueryString(): void
    {
        // store the query string local, so we don't alter it.
        $queryString = trim(Model::getRequest()->getPathInfo(), '/');

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
            $url = Navigation::getUrl(404);

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

    private function determineLanguage(string $queryString): string
    {
        if (!$this->getContainer()->getParameter('site.multilanguage')) {
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

            Model::getSession()->set('frontend_language', $language);

            return $language;
        }

        $cookie = $this->getContainer()->get('fork.cookie');
        if ($cookie->has('frontend_language')
            && in_array($cookie->get('frontend_language'), $redirectLanguages, true)
        ) {
            $this->redirectToLanguage($cookie->get('frontend_language'));
        }

        // default browser language
        $language = Language::getBrowserLanguage();
        $this->setLanguageCookie($language);
        $this->redirectToLanguage($language);
    }

    private function setLanguageCookie(string $language): void
    {
        try {
            self::getContainer()->get('fork.cookie')->set('frontend_language', $language);
        } catch (\RuntimeException $e) {
            // settings cookies isn't allowed, because this isn't a real problem we ignore the exception
        }
    }

    private function redirectToLanguage(string $language): void
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

    private function handleRedirects(array $pageInfo): void
    {
        // is this an internal redirect?
        if (isset($pageInfo['redirect_page_id']) && $pageInfo['redirect_page_id'] !== '') {
            // get url for item
            $newPageUrl = Navigation::getUrl((int) $pageInfo['redirect_page_id']);
            $errorURL = Navigation::getUrl(404);

            // not an error?
            if ($newPageUrl !== $errorURL) {
                // redirect
                throw new RedirectException(
                    'Redirect',
                    new RedirectResponse(
                        $newPageUrl,
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

    private function setPages(array $pages = []): void
    {
        $this->pages = $pages;
    }

    private function setParameters(array $parameters = []): void
    {
        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

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
            $url = Navigation::getUrl(404);

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
