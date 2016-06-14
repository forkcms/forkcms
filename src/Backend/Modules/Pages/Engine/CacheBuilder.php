<?php

namespace Backend\Modules\Pages\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * In this file, the pages cache is built
 *
 * @author Wouter Sioen <wouter@wijs.be>
 */
class CacheBuilder
{
    /**
     * @var \SpoonDatabase
     */
    protected $database;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    protected $blocks;
    protected $siteMapId;

    /**
     * @param \SpoonDatabase $database
     */
    public function __construct(\SpoonDatabase $database, CacheItemPoolInterface $cache)
    {
        $this->database = $database;
        $this->cache = $cache;
    }

    /**
     * Builds the pages cache
     *
     * @param string $language The language to build the cache for.
     */
    public function buildCache($language)
    {
        // kill existing caches so they can be re-generated
        $this->cache->deleteItems(array('keys_' . $language, 'navigation_' . $language));
        $keys = $this->getKeys($language);
        $navigation = $this->getNavigation($language);

        // build file with navigation structure to feed into editor
        $fs = new Filesystem();
        $cachePath = FRONTEND_CACHE_PATH . '/Navigation/';
        $fs->dumpFile(
            $cachePath . 'editor_link_list_' . $language . '.js',
            $this->dumpEditorLinkList($navigation, $keys, $language)
        );
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getKeys($language)
    {
        $item = $this->cache->getItem('keys_' . $language);
        if ($item->isHit()) {
            return $item->get();
        }

        list($keys, $navigation) = $this->getData($language);
        $item->set($keys);
        $this->cache->save($item);

        return $keys;
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getNavigation($language)
    {
        $item = $this->cache->getItem('navigation_' . $language);
        if ($item->isHit()) {
            return $item->get();
        }

        list($keys, $navigation) = $this->getData($language);
        $item->set($navigation);
        $this->cache->save($item);

        return $navigation;
    }

    /**
     * Fetches all data from the database
     *
     * @param string $language
     *
     * @return array tupple containing keys and navigation
     */
    protected function getData($language)
    {
        // get tree
        $levels = Model::getTree(array(0), null, 1, $language);

        $keys = array();
        $navigation = array();

        // loop levels
        foreach ($levels as $pages) {
            // loop all items on this level
            foreach ($pages as $pageId => $page) {
                $temp = $this->getPageData($keys, $page, $language);

                // add it
                $navigation[$page['type']][$page['parent_id']][$pageId] = $temp;
            }
        }

        // order by URL
        asort($keys);

        return array($keys, $navigation);
    }

    /**
     * Fetches the pagedata for a certain page array
     * It also adds the page data to the keys array
     *
     * @param  array  &$keys
     * @param  array  $page
     * @param  string $language
     *
     * @return array  An array containing more data for the page
     */
    protected function getPageData(&$keys, $page, $language)
    {
        $parentID = (int) $page['parent_id'];

        // init URLs
        $hasMultiLanguages = BackendModel::getContainer()->getParameter('site.multilanguage');
        $languageURL = ($hasMultiLanguages) ? '/' . $language . '/' : '/';
        $URL = (isset($keys[$parentID])) ? $keys[$parentID] : '';

        // home is special
        if ($page['id'] == 1) {
            $page['url'] = '';
            if ($hasMultiLanguages) {
                $languageURL = rtrim($languageURL, '/');
            }
        }

        // add it
        $keys[$page['id']] = trim($URL . '/' . $page['url'], '/');

        // unserialize
        if (isset($page['meta_data'])) {
            $page['meta_data'] = @unserialize($page['meta_data']);
        }

        // build navigation array
        $pageData = array(
            'page_id' => (int) $page['id'],
            'url' => $page['url'],
            'full_url' => $languageURL . $keys[$page['id']],
            'title' => $page['title'],
            'navigation_title' => $page['navigation_title'],
            'has_extra' => (bool) ($page['has_extra'] == 'Y'),
            'no_follow' => (bool) (isset($page['meta_data']['seo_follow']) && $page['meta_data']['seo_follow'] == 'nofollow'),
            'hidden' => (bool) ($page['hidden'] == 'Y'),
            'extra_blocks' => null,
            'has_children' => (bool) ($page['has_children'] == 'Y'),
        );

        $pageData['extra_blocks'] = $this->getPageExtraBlocks($page, $pageData);
        $pageData['tree_type'] = $this->getPageTreeType($page, $pageData);

        return $pageData;
    }

    protected function getPageTreeType($page, &$pageData)
    {
        // calculate tree-type
        $treeType = 'page';
        if ($page['hidden'] == 'Y') {
            $treeType = 'hidden';
        }

        // homepage should have a special icon
        if ($page['id'] == 1) {
            $treeType = 'home';
        } elseif ($page['id'] == 404) {
            $treeType = 'error';
        } elseif ($page['id'] < 404 && mb_substr_count($page['extra_ids'], $this->getSitemapId()) > 0) {
            // get extras
            $extraIDs = explode(',', $page['extra_ids']);

            // loop extras
            foreach ($extraIDs as $id) {
                // check if this is the sitemap id
                if ($id == $this->getSitemapId()) {
                    // set type
                    $treeType = 'sitemap';

                    // break it
                    break;
                }
            }
        }

        // any data?
        if (isset($page['data'])) {
            // get data
            $data = unserialize($page['data']);

            // internal alias?
            if (isset($data['internal_redirect']['page_id']) && $data['internal_redirect']['page_id'] != '') {
                $pageData['redirect_page_id'] = $data['internal_redirect']['page_id'];
                $pageData['redirect_code'] = $data['internal_redirect']['code'];
                $treeType = 'redirect';
            }

            // external alias?
            if (isset($data['external_redirect']['url']) && $data['external_redirect']['url'] != '') {
                $pageData['redirect_url'] = $data['external_redirect']['url'];
                $pageData['redirect_code'] = $data['external_redirect']['code'];
                $treeType = 'redirect';
            }

            // direct action?
            if (isset($data['is_action']) && $data['is_action']) {
                $treeType = 'direct_action';
            }
        }

        return $treeType;
    }

    protected function getPageExtraBlocks($page, $pageData)
    {
        // add extras to the page array
        if ($page['extra_ids'] !== null) {
            $blocks = $this->getBlocks();
            $ids = (array) explode(',', $page['extra_ids']);
            $pageBlocks = array();

            foreach ($ids as $id) {
                $id = (int) $id;

                // available in extras, so add it to the pageData-array
                if (isset($blocks[$id])) {
                    $pageBlocks[$id] = $blocks[$id];
                }
            }

            return $pageBlocks;
        }
    }

    /**
     * Returns an array containing all extras
     *
     * @return array
     */
    protected function getBlocks()
    {
        if (empty($this->blocks)) {
            $this->blocks = (array) $this->database->getRecords(
                'SELECT i.id, i.module, i.action
                 FROM modules_extras AS i
                 WHERE i.type = ? AND i.hidden = ?',
                array('block', 'N'),
                'id'
            );
        }

        return $this->blocks;
    }

    /**
     * Returns an array containing all widgets
     *
     * @return string
     */
    protected function getSitemapId()
    {
        if (empty($this->sitemapId)) {
            $widgets = (array) $this->database->getRecords(
                'SELECT i.id, i.module, i.action
                 FROM modules_extras AS i
                 WHERE i.type = ? AND i.hidden = ?',
                array('widget', 'N'),
                'id'
            );

            // search sitemap
            foreach ($widgets as $id => $row) {
                if ($row['action'] == 'Sitemap') {
                    $this->sitemapId = $id;
                    break;
                }
            }
        }

        return $this->sitemapId;
    }

    /**
     * Get the order
     *
     * @param  array  $navigation The navigation array.
     * @param  string $type       The type of navigation.
     * @param  int    $parentId   The Id to start from.
     * @param  array  $order      The array to hold the order.
     *
     * @return array
     */
    protected function getOrder($navigation, $type = 'page', $parentId = 0, $order = array())
    {
        // loop alle items for the type and parent
        foreach ($navigation[$type][$parentId] as $id => $page) {
            // add to array
            $order[$id] = $page['full_url'];

            // children of root/footer/meta-pages are stored under the page type
            if (($type == 'root' || $type == 'footer' || $type == 'meta') && isset($navigation['page'][$id])) {
                // process subpages
                $order = $this->getOrder($navigation, 'page', $id, $order);
            } elseif (isset($navigation[$type][$id])) {
                // process subpages
                $order = $this->getOrder($navigation, $type, $id, $order);
            }
        }

        // return
        return $order;
    }

    /**
     * Save the link list
     *
     * @param  array  $navigation The full navigation array
     * @param  array  $keys       The page keys
     * @param  string $language   The language to save the file for
     *
     * @return string             The full content for the cache file
     */
    protected function dumpEditorLinkList($navigation, $keys, $language)
    {
        // get the order
        foreach (array_keys($navigation) as $type) {
            $order[$type] = $this->getOrder($navigation, $type, 0);
        }

        // start building the cache file
        $editorLinkListString = $this->getCacheHeader(
            'the links that can be used by the editor'
        );

        // init var
        $links = array();

        // init var
        $cachedTitles = (array) $this->database->getPairs(
            'SELECT i.id, i.navigation_title
             FROM pages AS i
             WHERE i.id IN(' . implode(',', array_keys($keys)) . ')
             AND i.language = ? AND i.status = ?',
            array($language, 'active')
        );

        // loop the types in the order we want them to appear
        foreach (array('page', 'meta', 'footer', 'root') as $type) {
            // any pages?
            if (isset($order[$type])) {
                // loop pages
                foreach ($order[$type] as $pageId => $url) {
                    // skip if we don't have a title
                    if (!isset($cachedTitles[$pageId])) {
                        continue;
                    }

                    // get the title
                    $title = \SpoonFilter::htmlspecialcharsDecode($cachedTitles[$pageId]);

                    // split into chunks
                    $urlChunks = explode('/', $url);

                    // remove the language chunk
                    $hasMultiLanguages = BackendModel::getContainer()->getParameter('site.multilanguage');
                    $urlChunks = ($hasMultiLanguages) ? array_slice($urlChunks, 2) : array_slice($urlChunks, 1);

                    // subpage?
                    if (count($urlChunks) > 1) {
                        // loop while we have more then 1 chunk
                        while (count($urlChunks) > 1) {
                            // remove last chunk of the url
                            array_pop($urlChunks);

                            // build the temporary URL, so we can search for an id
                            $tempUrl = implode('/', $urlChunks);

                            // search the pageID
                            $tempPageId = array_search($tempUrl, $keys);

                            // prepend the title
                            if (!isset($cachedTitles[$tempPageId])) {
                                $title = ' > ' . $title;
                            } else {
                                $title = $cachedTitles[$tempPageId] . ' > ' . $title;
                            }
                        }
                    }

                    // add
                    $links[] = array($title, $url);
                }
            }
        }

        // add JSON-string
        $editorLinkListString .= 'var linkList = ' . json_encode($links) . ';';

        return $editorLinkListString;
    }

    /**
     * Gets the header for cache files
     *
     * @param  string $itContainsMessage A message about the content of the file
     *
     * @return string A comment to be used in the cache file
     */
    protected function getCacheHeader($itContainsMessage)
    {
        $cacheHeader = '/**' . "\n";
        $cacheHeader .= ' * This file is generated by Fork CMS, it contains' . "\n";
        $cacheHeader .= ' * ' . $itContainsMessage . "\n";
        $cacheHeader .= ' * ' . "\n";
        $cacheHeader .= ' * Fork CMS' . "\n";
        $cacheHeader .= ' * @generated ' . date('Y-m-d H:i:s') . "\n";
        $cacheHeader .= ' */' . "\n\n";

        return $cacheHeader;
    }
}
