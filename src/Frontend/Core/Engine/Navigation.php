<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\KernelLoader;
use Frontend\Core\Language\Language;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendAuthentication;

/**
 * This class will be used to build the navigation
 */
class Navigation extends KernelLoader
{
    /**
     * The excluded page ids. These will not be shown in the menu.
     *
     * @var array
     */
    private static $excludedPageIds = [];

    /**
     * The selected pageIds
     *
     * @var array
     */
    private static $selectedPageIds = [];

    /**
     * TwigTemplate instance
     *
     * @var TwigTemplate
     */
    protected $template;

    /**
     * URL instance
     *
     * @var Url
     */
    protected $url;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->template = $this->getContainer()->get('templating');
        $this->url = $this->getContainer()->get('url');

        // set selected ids
        $this->setSelectedPageIds();
    }

    /**
     * Creates a Backend URL for a given action and module
     * If you don't specify a language the current language will be used.
     *
     * @param string $action The action to build the URL for.
     * @param string $module The module to build the URL for.
     * @param string $language The language to use, if not provided we will use the working language.
     * @param array $parameters GET-parameters to use.
     * @param bool $urlencode Should the parameters be urlencoded?
     *
     * @return string
     */
    public static function getBackendUrlForBlock(
        string $action,
        string $module,
        string $language = null,
        array $parameters = null,
        bool $urlencode = true
    ): string {
        $language = $language ?? LANGUAGE;

        // add at least one parameter
        if (empty($parameters)) {
            $parameters['token'] = 'true';
        }

        if ($urlencode) {
            $parameters = array_map('rawurlencode', $parameters);
        }

        $queryString = '?' . http_build_query($parameters);

        // build the URL and return it
        return FrontendModel::get('router')->generate(
            'backend',
            ['_locale' => $language, 'module' => $module, 'action' => $action]
        ) . $queryString;
    }

    /**
     * Get the first child for a given parent
     *
     * @param int $pageId The pageID wherefore we should retrieve the first child.
     *
     * @return int
     */
    public static function getFirstChildId(int $pageId): int
    {
        // init var
        $navigation = self::getNavigation();

        // loop depths
        foreach ($navigation as $parent) {
            // no available, skip this element
            if (!isset($parent[$pageId])) {
                continue;
            }

            // get keys
            $keys = array_keys($parent[$pageId]);

            // get first item
            if (isset($keys[0])) {
                return $keys[0];
            }
        }

        // fallback
        return false;
    }

    /**
     * Get all footer links
     *
     * @return array
     */
    public static function getFooterLinks(): array
    {
        // get the navigation
        $navigation = self::getNavigation();
        $footerLinks = [];

        // validate
        if (!isset($navigation['footer'][0])) {
            return $footerLinks;
        }

        foreach ($navigation['footer'][0] as $id => $data) {
            // skip hidden pages
            if ($data['hidden']) {
                continue;
            }

            // add
            $footerLinks[] = [
                'id' => $id,
                'url' => self::getUrl($id),
                'title' => $data['title'],
                'navigation_title' => $data['navigation_title'],
                'selected' => in_array($id, self::$selectedPageIds, true),
            ];
        }

        return $footerLinks;
    }

    /**
     * Get the page-keys
     *
     * @param string $language The language wherefore the navigation should be loaded,
     *                         if not provided we will load the language that was provided in the URL.
     *
     * @return array
     */
    public static function getKeys(string $language = null): array
    {
        return BackendPagesModel::getCacheBuilder()->getKeys($language ?? LANGUAGE);
    }

    /**
     * Get the navigation-items
     *
     * @param string $language The language wherefore the keys should be loaded,
     *                         if not provided we will load the language that was provided in the URL.
     *
     * @return array
     */
    public static function getNavigation(string $language = null): array
    {
        return BackendPagesModel::getCacheBuilder()->getNavigation($language ?? LANGUAGE);
    }

    /**
     * Check if we have meta navigation and that it is enabled
     *
     * @param array $navigation
     *
     * @return bool
     */
    private static function hasMetaNavigation(array $navigation): bool
    {
        return isset($navigation['meta']) && Model::get('fork.settings')->get('Pages', 'meta_navigation', true);
    }

    /**
     * Get navigation HTML
     *
     * @param string $type The type of navigation the HTML should be build for.
     * @param int $parentId The parentID to start of.
     * @param int $depth The maximum depth to parse.
     * @param array $excludeIds PageIDs to be excluded.
     * @param string $template The template that will be used.
     * @param int $depthCounter A counter that will hold the current depth.
     *
     * @throws Exception
     *
     * @return string
     */
    public static function getNavigationHTML(
        string $type = 'page',
        int $parentId = 0,
        int $depth = null,
        array $excludeIds = [],
        string $template = 'Core/Layout/Templates/Navigation.html.twig',
        int $depthCounter = 1
    ): string {
        // get navigation
        $navigation = self::getNavigation();

        // merge the exclude ids with the previously set exclude ids
        $excludeIds = array_merge($excludeIds, self::$excludedPageIds);

        // meta-navigation is requested but meta isn't enabled
        if ($type === 'meta' && !self::hasMetaNavigation($navigation)) {
            return '';
        }

        // validate
        if (!isset($navigation[$type])) {
            throw new Exception(
                'This type (' . $type . ') isn\'t a valid navigation type. Possible values are: page, footer, meta.'
            );
        }

        if (!isset($navigation[$type][$parentId])) {
            throw new Exception('The parent (' . $parentId . ') doesn\'t exists.');
        }

        // special construction to merge home with its immediate children
        $mergedHome = false;
        while (true) {
            // loop elements
            foreach ($navigation[$type][$parentId] as $id => $page) {
                // home is a special item, it should live on the same depth
                if (!$mergedHome && (int) $page['page_id'] === 1) {
                    // extra checks otherwise exceptions will wbe triggered.
                    if (!isset($navigation[$type][$parentId])
                        || !is_array($navigation[$type][$parentId])) {
                        $navigation[$type][$parentId] = [];
                    }
                    if (!isset($navigation[$type][$page['page_id']])
                        || !is_array($navigation[$type][$page['page_id']])
                    ) {
                        $navigation[$type][$page['page_id']] = [];
                    }

                    // add children
                    $navigation[$type][$parentId] = array_merge(
                        $navigation[$type][$parentId],
                        $navigation[$type][$page['page_id']]
                    );

                    // mark as merged
                    $mergedHome = true;

                    // restart loop
                    continue 2;
                }

                // not hidden and not an action
                if ($page['hidden'] || $page['tree_type'] === 'direct_action') {
                    unset($navigation[$type][$parentId][$id]);
                    continue;
                }

                // authentication
                if (isset($page['data'])) {
                    // unserialize data
                    $page['data'] = unserialize($page['data']);
                    // if auth_required isset and is true
                    if (isset($page['data']['auth_required']) && $page['data']['auth_required']) {
                        // is profile logged? unset
                        if (!FrontendAuthentication::isLoggedIn()) {
                            unset($navigation[$type][$parentId][$id]);
                            continue;
                        }
                        // check if group auth is set
                        if (!empty($page['data']['auth_groups'])) {
                            $inGroup = false;
                            // loop group and set value true if one is found
                            foreach ($page['data']['auth_groups'] as $group) {
                                if (FrontendAuthentication::getProfile()->isInGroup($group)) {
                                    $inGroup = true;
                                }
                            }
                            // unset page if not in any of the groups
                            if (!$inGroup) {
                                unset($navigation[$type][$parentId][$id]);
                            }
                        }
                    }
                }

                // some ids should be excluded
                if (in_array($page['page_id'], $excludeIds)) {
                    unset($navigation[$type][$parentId][$id]);
                    continue;
                }

                // if the item is in the selected page it should get an selected class
                $navigation[$type][$parentId][$id]['selected'] = in_array(
                    $page['page_id'],
                    self::$selectedPageIds
                );

                // add nofollow attribute if needed
                $navigation[$type][$parentId][$id]['nofollow'] = $page['no_follow'];

                // meta and footer subpages have the "page" type
                $subType = ($type === 'meta' || $type === 'footer') ? 'page' : $type;

                // fetch children if needed
                if (($depthCounter + 1 <= $depth || $depth === null)
                    && (int) $page['page_id'] !== 1
                    && isset($navigation[$subType][$page['page_id']])
                ) {
                    $navigation[$type][$parentId][$id]['children'] = self::getNavigationHTML(
                        $subType,
                        $page['page_id'],
                        $depth,
                        (array) $excludeIds,
                        $template,
                        $depthCounter + 1
                    );
                } else {
                    $navigation[$type][$parentId][$id]['children'] = false;
                }

                $navigation[$type][$parentId][$id]['parent_id'] = $parentId;
                $navigation[$type][$parentId][$id]['depth'] = $depthCounter;
                $navigation[$type][$parentId][$id]['link'] = static::getUrl($page['page_id']);

                // is this an internal redirect?
                if (isset($page['redirect_page_id']) && $page['redirect_page_id'] !== '') {
                    $navigation[$type][$parentId][$id]['link'] = static::getUrl(
                        (int) $page['redirect_page_id']
                    );
                }

                // is this an external redirect?
                if (isset($page['redirect_url']) && $page['redirect_url'] !== '') {
                    $navigation[$type][$parentId][$id]['link'] = $page['redirect_url'];
                }
            }

            // break the loop (it is only used for the special construction with home)
            break;
        }

        // return parsed content
        return Model::get('templating')->render(
            $template,
            ['navigation' => $navigation[$type][$parentId]]
        );
    }

    /**
     * Get a menuId for an specified URL
     *
     * @param string $url The URL wherefore you want a pageID.
     * @param string $language The language wherefore the pageID should be retrieved,
     *                          if not provided we will load the language that was provided in the URL.
     *
     * @return int
     */
    public static function getPageId(string $url, string $language = null): int
    {
        // redefine
        $url = trim($url, '/');

        // get menu items array
        $keys = self::getKeys($language ?? LANGUAGE);

        // get key
        $key = array_search($url, $keys, true);

        // return 404 if we don't known a valid Id
        if ($key === false) {
            return 404;
        }

        // return the real Id
        return (int) $key;
    }

    /**
     * Get more info about a page
     *
     * @param int $requestedPageId The pageID wherefore you want more information.
     *
     * @return array|bool
     */
    public static function getPageInfo(int $requestedPageId)
    {
        // get navigation
        $navigation = self::getNavigation();

        // loop levels
        foreach ($navigation as $level) {
            // loop parents
            foreach ($level as $parentId => $children) {
                // loop children
                foreach ($children as $pageId => $page) {
                    // return if this is the requested page
                    if ($requestedPageId === (int) $pageId) {
                        // set return
                        $pageInfo = $page;
                        $pageInfo['page_id'] = $pageId;
                        $pageInfo['parent_id'] = $parentId;

                        // return
                        return $pageInfo;
                    }
                }
            }
        }

        // fallback
        return false;
    }

    /**
     * Get URL for a given pageId
     *
     * @param int $pageId The pageID wherefore you want the URL.
     * @param string $language The language wherein the URL should be retrieved,
     *                         if not provided we will load the language that was provided in the URL.
     *
     * @return string
     */
    public static function getUrl(int $pageId, string $language = null): string
    {
        $language = $language ?? LANGUAGE;

        // init URL
        $url = FrontendModel::getContainer()->getParameter('site.multilanguage') ? '/' . $language . '/' : '/';

        // get the menuItems
        $keys = self::getKeys($language);

        // get the URL, if it doesn't exist return 404
        if ($pageId !== 404 && !isset($keys[$pageId])) {
            return self::getUrl(404, $language);
        }

        if (empty($keys)) {
            return urldecode($url . 404);
        }

        // return the URL
        return urldecode($url . $keys[$pageId]);
    }

    /**
     * Get the URL for a give module & action combination
     *
     * @param string $module The module wherefore the URL should be build.
     * @param string $action The specific action wherefore the URL should be build.
     * @param string $language The language wherein the URL should be retrieved,
     *                         if not provided we will load the language that was provided in the URL.
     * @param array $data An array with keys and values that partially or fully match the data of the block.
     *                    If it matches multiple versions of that block it will just return the first match.
     *
     * @return string
     */
    public static function getUrlForBlock(
        string $module,
        string $action = null,
        string $language = null,
        array $data = null
    ): string {
        $language = $language ?? LANGUAGE;
        // init var
        $pageIdForUrl = null;

        // get the menuItems
        $navigation = self::getNavigation($language);

        $dataMatch = false;
        // loop types
        foreach ($navigation as $level) {
            // loop level
            foreach ($level as $pages) {
                // loop pages
                foreach ($pages as $pageId => $properties) {
                    // only process pages with extra_blocks that are visible
                    if (!isset($properties['extra_blocks']) || $properties['hidden']) {
                        continue;
                    }

                    // loop extras
                    foreach ($properties['extra_blocks'] as $extra) {
                        // direct link?
                        if ($extra['module'] === $module && $extra['action'] === $action && $extra['action'] !== null) {
                            // if there is data check if all the requested data matches the extra data
                            if ($data !== null && isset($extra['data'])
                                && array_intersect_assoc($data, (array) $extra['data']) !== $data) {
                                // It is the correct action but has the wrong data
                                continue;
                            }
                            // exact page was found, so return
                            return self::getUrl($properties['page_id'], $language);
                        }

                        if ($extra['module'] === $module && $extra['action'] === null) {
                            // if there is data check if all the requested data matches the extra data
                            if ($data !== null && isset($extra['data'])) {
                                if (array_intersect_assoc($data, (array) $extra['data']) !== $data) {
                                    // It is the correct module but has the wrong data
                                    continue;
                                }

                                $pageIdForUrl = (int) $pageId;
                                $dataMatch = true;
                            }

                            if ($data === null && $extra['data'] === null) {
                                $pageIdForUrl = (int) $pageId;
                                $dataMatch = true;
                            }

                            if (!$dataMatch) {
                                $pageIdForUrl = (int) $pageId;
                            }
                        }
                    }
                }
            }
        }

        // pageId still null?
        if ($pageIdForUrl === null) {
            return self::getUrl(404, $language);
        }

        // build URL
        $url = self::getUrl($pageIdForUrl, $language);

        // append action
        if ($action !== null) {
            $url .= '/' . Language::act(\SpoonFilter::toCamelCase($action));
        }

        // return the URL
        return $url;
    }

    /**
     * Fetch the first direct link to an extra id
     *
     * @param int $id The id of the extra.
     * @param string $language The language wherein the URL should be retrieved,
     *                         if not provided we will load the language that was provided in the URL.
     *
     * @return string
     */
    public static function getUrlForExtraId(int $id, string $language = null): string
    {
        $language = $language ?? LANGUAGE;
        // get the menuItems
        $navigation = self::getNavigation($language);

        // loop types
        foreach ($navigation as $level) {
            // loop level
            foreach ($level as $pages) {
                // loop pages
                foreach ($pages as $properties) {
                    // no extra_blocks available, so skip this item
                    if (!isset($properties['extra_blocks'])) {
                        continue;
                    }

                    // loop extras
                    foreach ($properties['extra_blocks'] as $extra) {
                        // direct link?
                        if ((int) $extra['id'] === $id) {
                            // exact page was found, so return
                            return self::getUrl($properties['page_id'], $language);
                        }
                    }
                }
            }
        }

        // fallback
        return self::getUrl(404, $language);
    }

    /**
     * This function lets you add ignored pages
     *
     * @param mixed $pageIds This can be a single page id or this can be an array with page ids.
     */
    public static function setExcludedPageIds($pageIds): void
    {
        $pageIds = (array) $pageIds;

        // go trough the page ids to add them to the excluded page ids for later usage
        foreach ($pageIds as $pageId) {
            self::$excludedPageIds[] = $pageId;
        }
    }

    public function setSelectedPageIds(): void
    {
        // get pages
        $pages = (array) $this->url->getPages();

        // no pages, means we're at the homepage
        if (empty($pages)) {
            self::$selectedPageIds[] = 1;

            return;
        }

        // loop pages
        while (!empty($pages)) {
            // get page id
            $pageId = self::getPageId((string) implode('/', $pages));

            // add pageId into selected items
            if ($pageId !== false) {
                self::$selectedPageIds[] = $pageId;
            }

            // remove last element
            array_pop($pages);
        }
    }
}
