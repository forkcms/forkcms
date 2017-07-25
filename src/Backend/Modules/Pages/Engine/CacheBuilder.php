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
    protected $sitemapId;

    public function __construct(\SpoonDatabase $database, CacheItemPoolInterface $cache)
    {
        $this->database = $database;
        $this->cache = $cache;
    }

    public function buildCache(string $language): void
    {
        // kill existing caches so they can be re-generated
        $this->cache->deleteItems(['keys_' . $language, 'navigation_' . $language]);
        $keys = $this->getKeys($language);
        $navigation = $this->getNavigation($language);

        // build file with navigation structure to feed into editor
        $filesystem = new Filesystem();
        $cachePath = FRONTEND_CACHE_PATH . '/Navigation/';
        $filesystem->dumpFile(
            $cachePath . 'editor_link_list_' . $language . '.js',
            $this->dumpEditorLinkList($navigation, $keys, $language)
        );
    }

    public function getKeys(string $language): array
    {
        $item = $this->cache->getItem('keys_' . $language);
        if ($item->isHit()) {
            return $item->get();
        }

        $keys = $this->getData($language)[0];
        $item->set($keys);
        $this->cache->save($item);

        return $keys;
    }

    public function getNavigation(string $language): array
    {
        $item = $this->cache->getItem('navigation_' . $language);
        if ($item->isHit()) {
            return $item->get();
        }

        $navigation = $this->getData($language)[1];
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
    protected function getData(string $language): array
    {
        // get tree
        $levels = Model::getTree([0], null, 1, $language);

        $keys = [];
        $navigation = [];

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

        return [$keys, $navigation];
    }

    /**
     * Fetches the pagedata for a certain page array
     * It also adds the page data to the keys array
     *
     * @param array &$keys
     * @param array $page
     * @param string $language
     *
     * @return array An array containing more data for the page
     */
    protected function getPageData(array &$keys, array $page, string $language): array
    {
        $parentID = (int) $page['parent_id'];

        // init URLs
        $hasMultiLanguages = BackendModel::getContainer()->getParameter('site.multilanguage');
        $languageUrl = ($hasMultiLanguages) ? '/' . $language . '/' : '/';
        $url = (isset($keys[$parentID])) ? $keys[$parentID] : '';

        // home is special
        if ($page['id'] == 1) {
            $page['url'] = '';
            if ($hasMultiLanguages) {
                $languageUrl = rtrim($languageUrl, '/');
            }
        }

        // add it
        $keys[$page['id']] = trim($url . '/' . $page['url'], '/');

        // build navigation array
        $pageData = [
            'page_id' => (int) $page['id'],
            'url' => $page['url'],
            'full_url' => $languageUrl . $keys[$page['id']],
            'title' => $page['title'],
            'navigation_title' => $page['navigation_title'],
            'has_extra' => (bool) $page['has_extra'],
            'no_follow' => $page['seo_follow'] === 'nofollow',
            'hidden' => (bool) $page['hidden'],
            'extra_blocks' => null,
            'has_children' => (bool) $page['has_children'],
        ];

        $pageData['extra_blocks'] = $this->getPageExtraBlocks($page);
        $pageData['tree_type'] = $this->getPageTreeType($page, $pageData);

        return $pageData;
    }

    protected function getPageTreeType(array $page, array &$pageData): string
    {
        // calculate tree-type
        $treeType = 'page';
        if ($page['hidden']) {
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

    protected function getPageExtraBlocks(array $page): array
    {
        $pageBlocks = [];

        if ($page['extra_ids'] === null) {
            return $pageBlocks;
        }

        $blocks = $this->getBlocks();
        $ids = (array) explode(',', $page['extra_ids']);

        foreach ($ids as $id) {
            $id = (int) $id;

            // available in extras, so add it to the pageData-array
            if (isset($blocks[$id])) {
                $pageBlocks[$id] = $blocks[$id];
            }
        }

        return $pageBlocks;
    }

    protected function getBlocks(): array
    {
        if (empty($this->blocks)) {
            $this->blocks = (array) $this->database->getRecords(
                'SELECT i.id, i.module, i.action, i.data
                 FROM modules_extras AS i
                 WHERE i.type = ? AND i.hidden = ?',
                ['block', false],
                'id'
            );

            $this->blocks = array_map(
                function (array $block) {
                    if ($block['data'] === null) {
                        return $block;
                    }

                    $block['data'] = unserialize($block['data']);

                    return $block;
                },
                $this->blocks
            );
        }

        return $this->blocks;
    }

    protected function getSitemapId(): int
    {
        if (empty($this->sitemapId)) {
            $widgets = (array) $this->database->getRecords(
                'SELECT i.id, i.module, i.action
                 FROM modules_extras AS i
                 WHERE i.type = ? AND i.hidden = ?',
                ['widget', false],
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

        return (int) $this->sitemapId;
    }

    protected function getOrder(
        array $navigation,
        string $type = 'page',
        int $parentId = 0,
        array $order = []
    ): array {
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

    protected function dumpEditorLinkList(array $navigation, array $keys, string $language): string
    {
        $order = [];
        // get the order
        foreach (array_keys($navigation) as $type) {
            $order[$type] = $this->getOrder($navigation, $type, 0);
        }

        // start building the cache file
        $editorLinkListString = $this->getCacheHeader(
            'the links that can be used by the editor'
        );

        // init var
        $links = [];

        // init var
        $cachedTitles = (array) $this->database->getPairs(
            'SELECT i.id, i.navigation_title
             FROM pages AS i
             WHERE i.id IN(' . implode(',', array_keys($keys)) . ')
             AND i.language = ? AND i.status = ?',
            [$language, 'active']
        );

        // loop the types in the order we want them to appear
        foreach (['page', 'meta', 'footer', 'root'] as $type) {
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
                    $links[] = [$title, $url];
                }
            }
        }

        // add JSON-string
        $editorLinkListString .= 'var linkList = ' . json_encode($links) . ';';

        return $editorLinkListString;
    }

    protected function getCacheHeader(string $itContainsMessage): string
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
