<?php

namespace Backend\Modules\Pages\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\Page\Status;
use Backend\Modules\Pages\Domain\Page\Page;
use Common\Locale;
use Doctrine\ORM\NoResultException;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;
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

    /**
     * @var ModuleExtraRepository
     */
    private $moduleExtraRepository;

    protected $blocks;
    protected $sitemapId;

    public function __construct(
        \SpoonDatabase $database,
        CacheItemPoolInterface $cache,
        ModuleExtraRepository $moduleExtraRepository
    ) {
        $this->database = $database;
        $this->cache = $cache;
        $this->moduleExtraRepository = $moduleExtraRepository;
    }

    public function buildCache(Locale $locale): void
    {
        // kill existing caches so they can be re-generated
        $this->cache->deleteItems(['keys_' . $locale, 'navigation_' . $locale]);
        $keys = $this->getKeys($locale);
        $navigation = $this->getNavigation($locale);

        // build file with navigation structure to feed into editor
        $filesystem = new Filesystem();
        $cachePath = FRONTEND_CACHE_PATH . '/Navigation/';
        $filesystem->dumpFile(
            $cachePath . 'editor_link_list_' . $locale . '.js',
            $this->dumpEditorLinkList($navigation, $keys, $locale)
        );
    }

    public function getKeys(Locale $locale): array
    {
        $item = $this->cache->getItem('keys_' . $locale);
        if ($item->isHit()) {
            return $item->get();
        }

        $keys = $this->getData($locale)[0];
        $item->set($keys);
        $this->cache->save($item);

        return $keys;
    }

    public function getNavigation(Locale $locale): array
    {
        $item = $this->cache->getItem('navigation_' . $locale);
        if ($item->isHit()) {
            return $item->get();
        }

        $navigation = $this->getData($locale)[1];
        $item->set($navigation);

        $pageRepository = BackendModel::getContainer()->get(PageRepository::class);
        $cacheExpirationDate = $pageRepository->getCacheExpirationDate();
        $item->expiresAt($cacheExpirationDate);

        $this->cache->save($item);

        return $navigation;
    }

    /**
     * Fetches all data from the database
     *
     * @param Locale $locale
     *
     * @return array tupple containing keys and navigation
     */
    protected function getData(Locale $locale): array
    {
        // get tree
        $levels = Model::getTree([0], null, 1, $locale);

        $keys = [];
        $navigation = [];

        // loop levels
        foreach ($levels as $pages) {
            // loop all items on this level
            foreach ($pages as $pageId => $page) {
                $navigation[(string) $page['type']][$page['parent_id']][$pageId] = $this->getPageData(
                    $keys,
                    $page,
                    $locale
                );
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
     * @param Locale $locale
     *
     * @return array An array containing more data for the page
     */
    protected function getPageData(array &$keys, array $page, Locale $locale): array
    {
        $parentID = (int) $page['parent_id'];

        // init URLs
        $hasMultiLanguages = BackendModel::getContainer()->getParameter('site.multilanguage');
        $languageUrl = $hasMultiLanguages ? '/' . $locale . '/' : '/';
        $url = $keys[$parentID] ?? '';

        // home is special
        if ($page['id'] == Page::HOME_PAGE_ID) {
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
            'allow_children' => (bool) $page['allow_children'],
            'allow_move' => (bool) $page['allow_move'],
            'data' => $page['data'],
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
        if ($page['id'] == Page::HOME_PAGE_ID) {
            $treeType = 'home';
        } elseif ($page['id'] == Page::ERROR_PAGE_ID) {
            $treeType = 'error';
        } elseif ($page['id'] < Page::ERROR_PAGE_ID
                  && mb_substr_count($page['extra_ids'], $this->getSitemapId()) > 0) {
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
            $data = unserialize($page['data'], ['allowed_classes' => false]);

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
            $this->blocks = $this->moduleExtraRepository->getBlocks();
        }

        return $this->blocks;
    }

    protected function getSitemapId(): int
    {
        if ($this->sitemapId === null) {
            try {
                $this->sitemapId = $this->moduleExtraRepository->findIdForModuleAndAction('Pages', 'Sitemap');
            } catch (NoResultException $e) {
                throw new RuntimeException('Sitemap action of the Pages module not found');
            }
        }

        return $this->sitemapId;
    }

    protected function getOrder(
        array $navigation,
        string $type = 'page',
        int $parentId = Page::NO_PARENT_PAGE_ID,
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

    protected function dumpEditorLinkList(array $navigation, array $keys, Locale $locale): string
    {
        $order = [];
        // get the order
        foreach (array_keys($navigation) as $type) {
            $order[$type] = $this->getOrder($navigation, $type);
        }

        // start building the cache file
        $editorLinkListString = $this->getCacheHeader(
            'the links that can be used by the editor'
        );

        // init var
        $links = [];

        $pageRepository = BackendModel::getContainer()->get(PageRepository::class);
        $cachedTitles = $pageRepository->getNavigationTitles(array_keys($keys), $locale, Status::active());

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
                                $title = ' → ' . $title;
                            } else {
                                $title = $cachedTitles[$tempPageId] . ' → ' . $title;
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
