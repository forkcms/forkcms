<?php

namespace Backend\Modules\Pages\Engine;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BL;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Command\CopyContentBlocksToOtherLocale;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Location\Command\CopyLocationWidgetsToOtherLocale;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\Page\Status;
use Backend\Modules\Pages\Domain\PageBlock\PageBlock;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockType;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Common\Doctrine\Entity\Meta;
use Common\Doctrine\Repository\MetaRepository;
use DateTime;
use ForkCMS\App\ForkController;
use Frontend\Core\Language\Language as FrontendLanguage;
use InvalidArgumentException;
use RuntimeException;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Filesystem\Filesystem;

/**
 * In this file we store all generic functions that we will be using in the PagesModule
 */
class Model
{
    const NO_PARENT_PAGE_ID = 0;

    const TYPE_OF_DROP_BEFORE = 'before';
    const TYPE_OF_DROP_AFTER = 'after';
    const TYPE_OF_DROP_INSIDE = 'inside';
    const POSSIBLE_TYPES_OF_DROP = [
        self::TYPE_OF_DROP_BEFORE,
        self::TYPE_OF_DROP_AFTER,
        self::TYPE_OF_DROP_INSIDE,
    ];

    const QUERY_BROWSE_RECENT =
        'SELECT i.id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM pages AS i
         WHERE i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC
         LIMIT ?';

    const QUERY_DATAGRID_BROWSE_DRAFTS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM pages AS i
         INNER JOIN
         (
             SELECT MAX(i.revision_id) AS revision_id
             FROM pages AS i
             WHERE i.status = ? AND i.user_id = ? AND i.language = ?
             GROUP BY i.id
         ) AS p
         WHERE i.revision_id = p.revision_id';

    const QUERY_BROWSE_REVISIONS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM pages AS i
         WHERE i.id = ? AND i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    const QUERY_DATAGRID_BROWSE_SPECIFIC_DRAFTS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM pages AS i
         WHERE i.id = ? AND i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    const QUERY_BROWSE_TEMPLATES =
        'SELECT i.id, i.label AS title
         FROM pages_templates AS i
         WHERE i.theme = ?
         ORDER BY i.label ASC';

    public static function getCacheBuilder(): CacheBuilder
    {
        static $cacheBuilder = null;
        if ($cacheBuilder === null) {
            $cacheBuilder = new CacheBuilder(
                BackendModel::get('database'),
                BackendModel::get('cache.pool'),
                BackendModel::get(ModuleExtraRepository::class)
            );
        }

        return $cacheBuilder;
    }

    public static function buildCache(string $language = null): void
    {
        $cacheBuilder = static::getCacheBuilder();
        $cacheBuilder->buildCache($language ?? BL::getWorkingLanguage());
    }

    public static function copy(string $fromLanguage, string $toLanguage): void
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        /** @var MessageBus $commanBus */
        $commandBus = BackendModel::get('command_bus');

        /** @var MetaRepository $metaRepository */
        $metaRepository = BackendModel::get('fork.repository.meta');

        $toLocale = Locale::fromString($toLanguage);
        $fromLocale = Locale::fromString($fromLanguage);

        // copy contentBlocks and get copied contentBlockIds
        $copyContentBlocks = new CopyContentBlocksToOtherLocale($toLocale, $fromLocale);
        $commandBus->handle($copyContentBlocks);
        $contentBlockIds = $copyContentBlocks->extraIdMap;

        // define old block ids
        $contentBlockOldIds = array_keys($contentBlockIds);

        if (BackendModel::isModuleInstalled('Location')) {
            // copy location widgets and get copied widget ids
            $copyLocationWidgets = new CopyLocationWidgetsToOtherLocale($toLocale, $fromLocale);
            $commandBus->handle($copyLocationWidgets);
            $locationWidgetIds = $copyLocationWidgets->extraIdMap;

            // define old block ids
            $locationWidgetOldIds = array_keys($locationWidgetIds);
        }

        // get all old pages
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $pages = $pageRepository->findBy(['language' => $toLanguage, 'status' => Page::ACTIVE]);

        // delete existing pages
        /** @var Page $page */
        foreach ($pages as $page) {
            // redefine
            $activePage = $page->getId();

            // get revision ids
            $pagesById = $pageRepository->findBy(['id' => $activePage, 'language' => $toLanguage]);
            $revisionIDs = array_map(
                function (Page $page) {
                    return $page->getRevisionId();
                },
                $pagesById
            );

            /** @var Page $item */
            foreach ($pagesById as $item) {
                $metaRepository->remove($item->getMeta());
            }

            // delete blocks and their revisions
            if (!empty($revisionIDs)) {
                /** @var PageBlockRepository $pageBlockRepository */
                $pageBlockRepository = BackendModel::get(PageBlockRepository::class);
                $pageBlockRepository->deleteByRevisionIds($revisionIDs);
            }

            // delete page and the revisions
            /** @var Page $item */
            foreach ($pagesById as $item) {
                $pageRepository->remove($item);
            }
        }

        // delete search indexes
        $database->delete('search_index', 'module = ? AND language = ?', ['pages', $toLanguage]);

        // get all active pages
        $activePages = $pageRepository->findBy(['language' => $fromLanguage, 'status' => Page::ACTIVE]);

        // loop
        /** @var Page $activePage */
        foreach ($activePages as $activePage) {
            // get data
            $sourceData = self::get($activePage->getId(), null, $fromLanguage);

            // get and build meta
            /** @var Meta $originalMeta */
            $originalMeta = $metaRepository->find($sourceData['meta_id']);

            $meta = Meta::copy($originalMeta);

            // Insert new meta
            $metaRepository->add($meta);
            $metaRepository->save($meta);

            // build page
            $page = new Page(
                $sourceData['id'],
                BackendAuthentication::getUser()->getUserId(),
                $sourceData['parent_id'],
                $sourceData['template_id'],
                $meta,
                $toLanguage,
                $sourceData['type'],
                $sourceData['title'],
                new DateTime(),
                $sourceData['sequence'],
                $sourceData['navigation_title_overwrite'],
                $sourceData['hidden'],
                Status::active(),
                $sourceData['type'],
                $sourceData['data'],
                $sourceData['allow_move'],
                $sourceData['allow_children'],
                $sourceData['allow_edit'],
                $sourceData['allow_delete']
            );

            // insert page, store the id, we need it when building the blocks
            $pageRepository->add($page);
            $pageRepository->save($page);

            $revisionId = $page->getRevisionId();

            $blocks = [];

            // get the blocks
            $sourceBlocks = self::getBlocks($activePage->getId(), null, $fromLanguage);

            // loop blocks
            foreach ($sourceBlocks as $sourceBlock) {
                // build block
                $block = $sourceBlock;
                $block['revision_id'] = $revisionId;
                $block['created_on'] = BackendModel::getUTCDate();
                $block['edited_on'] = BackendModel::getUTCDate();

                // Overwrite the extra_id of the old content block with the id of the new one
                if (in_array($block['extra_id'], $contentBlockOldIds)) {
                    $block['extra_id'] = $contentBlockIds[$block['extra_id']];
                }

                if (BackendModel::isModuleInstalled('Location')) {
                    // Overwrite the extra_id of the old location widget with the id of the new one
                    if (in_array($block['extra_id'], $locationWidgetOldIds)) {
                        $block['extra_id'] = $locationWidgetIds[$block['extra_id']];
                    }
                }

                // add block
                $blocks[] = $block;
            }

            // insert the blocks
            self::insertBlocks($blocks);

            $text = '';

            // build search-text
            foreach ($blocks as $block) {
                $text .= ' ' . $block['html'];
            }

            // add
            BackendSearchModel::saveIndex(
                'Pages',
                (int) $page->getId(),
                ['title' => $page->getTitle(), 'text' => $text],
                $toLanguage
            );

            // get tags
            $tags = BackendTagsModel::getTags('pages', $activePage->getId(), 'string', $fromLanguage);

            // save tags
            if ($tags !== '') {
                $saveWorkingLanguage = BL::getWorkingLanguage();

                // If we don't set the working language to the target language,
                // BackendTagsModel::getUrl() will use the current working
                // language, possibly causing unnecessary '-2' suffixes in
                // tags.url
                BL::setWorkingLanguage($toLanguage);

                BackendTagsModel::saveTags($page->getId(), $tags, 'pages', $toLanguage);
                BL::setWorkingLanguage($saveWorkingLanguage);
            }
        }

        // build cache
        self::buildCache($toLanguage);
    }

    /**
     * @deprecated Will become private since it is only used in this class
     */
    public static function createHtml(
        string $navigationType = 'page',
        int $depth = 0,
        int $parentId = BackendModel::HOME_PAGE_ID,
        string $html = ''
    ): string {
        $navigation = static::getCacheBuilder()->getNavigation(BL::getWorkingLanguage());

        // check if item exists
        if (isset($navigation[$navigationType][$depth][$parentId])) {
            // start html
            $html .= '<ul>' . "\n";

            // loop elements
            foreach ($navigation[$navigationType][$depth][$parentId] as $key => $aValue) {
                $html .= "\t<li>" . "\n";
                $html .= "\t\t" . '<a href="#">' . $aValue['navigation_title'] . '</a>' . "\n";

                // insert recursive here!
                if (isset($navigation[$navigationType][$depth + 1][$key])) {
                    $html .= self::createHtml(
                        $navigationType,
                        $depth + 1,
                        $parentId,
                        ''
                    );
                }

                // add html
                $html .= '</li>' . "\n";
            }

            // end html
            $html .= '</ul>' . "\n";
        }

        // return
        return $html;
    }

    /**
     * @param int $id The id of the page to delete.
     * @param string $language The language wherein the page will be deleted,
     *                           if not provided we will use the working language.
     * @param int $revisionId If specified the given revision will be deleted, used for deleting drafts.
     *
     * @return bool
     */
    public static function delete(int $id, string $language = null, int $revisionId = null): bool
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        /** @var MetaRepository $metaRepository */
        $metaRepository = BackendModel::get('fork.repository.meta');

        $language = $language ?? BL::getWorkingLanguage();

        // get record
        $page = self::get($id, $revisionId, $language);

        // validate
        if (empty($page)) {
            return false;
        }
        if (!$page['allow_delete']) {
            return false;
        }

        // get revision ids
        $pages = $pageRepository->findBy(['id' => $id, 'language' => $language]);

        $revisionIDs = array_map(
            function (Page $page) {
                return $page->getRevisionId();
            },
            $pages
        );

        // delete meta records
        foreach ($pages as $page) {
            $metaRepository->remove($page->getMeta());
        }

        // delete blocks and their revisions
        if (!empty($revisionIDs)) {
            /** @var PageBlockRepository $pageBlockRepository */
            $pageBlockRepository = BackendModel::get(PageBlockRepository::class);
            $pageBlockRepository->deleteByRevisionIds($revisionIDs);
        }

        // delete page and the revisions
        if (!empty($revisionIDs)) {
            $pageRepository->deleteByRevisionIds($revisionIDs);
        }

        // delete tags
        BackendTagsModel::saveTags($id, '', 'Pages');

        // return
        return true;
    }

    public static function exists(int $pageId): bool
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $page = $pageRepository->findOneBy(
            [
                'id' => $pageId,
                'language' => BL::getWorkingLanguage(),
                'status' => [Page::ACTIVE, Page::DRAFT],
            ]
        );

        return $page instanceof Page;
    }

    /**
     * Get the data for a record
     *
     * @param int $pageId The Id of the page to fetch.
     * @param int $revisionId
     * @param string $language The language to use while fetching the page.
     *
     * @return mixed False if the record can't be found, otherwise an array with all data.
     */
    public static function get(int $pageId, int $revisionId = null, string $language = null)
    {
        // fetch revision if not specified
        if ($revisionId === null) {
            $revisionId = self::getLatestRevision($pageId, $language);
        }

        // redefine
        $language = $language ?? BL::getWorkingLanguage();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $page = $pageRepository->getOne($pageId, $revisionId, $language);

        // no page?
        if ($page === null) {
            return false;
        }

        return $page;
    }

    public static function isForbiddenToDelete(int $pageId): bool
    {
        return in_array($pageId, [BackendModel::HOME_PAGE_ID, BackendModel::ERROR_PAGE_ID], true);
    }

    public static function isForbiddenToMove(int $pageId): bool
    {
        return in_array($pageId, [BackendModel::HOME_PAGE_ID, BackendModel::ERROR_PAGE_ID], true);
    }

    public static function isForbiddenToHaveChildren(int $pageId): bool
    {
        return $pageId === BackendModel::ERROR_PAGE_ID;
    }

    public static function getBlocks(int $pageId, int $revisionId = null, string $language = null): array
    {
        // fetch revision if not specified
        if ($revisionId === null) {
            $revisionId = self::getLatestRevision($pageId, $language);
        }

        // redefine
        $language = $language ?? BL::getWorkingLanguage();

        /** @var PageBlockRepository $pageBlockRepository */
        $pageBlockRepository = BackendModel::get(PageBlockRepository::class);
        $pageBlocks = $pageBlockRepository->getBlocksForPage($pageId, $revisionId, $language);

        return $pageBlocks;
    }

    public static function getByTag(int $tagId): array
    {
        // get the items
        $items = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS url, i.title AS name, mt.module
             FROM modules_tags AS mt
             INNER JOIN tags AS t ON mt.tag_id = t.id
             INNER JOIN pages AS i ON mt.other_id = i.id
             WHERE mt.module = ? AND mt.tag_id = ? AND i.status = ?',
            ['pages', $tagId, 'active']
        );

        // loop items
        foreach ($items as &$row) {
            $row['url'] = BackendModel::createUrlForAction(
                'Edit',
                'Pages',
                null,
                ['id' => $row['url']]
            );
        }

        // return
        return $items;
    }

    /**
     * Get the id first child for a given parent
     */
    public static function getFirstChildId(int $parentId): ?int
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $page = $pageRepository->getFirstChild($parentId, Page::ACTIVE, BL::getWorkingLanguage());

        if ($page instanceof Page) {
            return $page->getId();
        }

        return null;
    }

    public static function getFullUrl(int $id): string
    {
        $keys = static::getCacheBuilder()->getKeys(BL::getWorkingLanguage());
        $hasMultiLanguages = BackendModel::getContainer()->getParameter('site.multilanguage');

        // available in generated file?
        if (isset($keys[$id])) {
            $url = $keys[$id];
        } elseif ($id == self::NO_PARENT_PAGE_ID) {
            // parent id 0 hasn't an url
            $url = '/';

            // multilanguages?
            if ($hasMultiLanguages) {
                $url = '/' . BL::getWorkingLanguage();
            }

            // return the unique URL!
            return $url;
        } else {
            // not available
            return false;
        }

        // if the is available in multiple languages we should add the current lang
        if ($hasMultiLanguages) {
            $url = '/' . BL::getWorkingLanguage() . '/' . $url;
        } else {
            // just prepend with slash
            $url = '/' . $url;
        }

        // return the unique URL!
        return urldecode($url);
    }

    public static function getLatestRevision(int $id, string $language = null): int
    {
        $language = $language ?? BL::getWorkingLanguage();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        return (int) $pageRepository->getLatestVersion($id, $language);
    }

    public static function getMaximumPageId($language = null): int
    {
        $language = $language ?? BL::getWorkingLanguage();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        return $pageRepository->getMaximumPageId($language, BackendAuthentication::getUser()->isGod());
    }

    public static function getMaximumSequence(int $parentId, string $language = null): int
    {
        $language = $language ?? BL::getWorkingLanguage();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        // get the maximum sequence inside a certain leaf
        return $pageRepository->getMaximumSequence($parentId, $language);
    }

    public static function getPagesForDropdown(string $language = null): array
    {
        $language = $language ?? BL::getWorkingLanguage();
        $titles = [];
        $sequences = [
            'pages' => [],
            'footer' => [],
        ];
        $keys = [];
        $pages = [];
        $pageTree = self::getTree([self::NO_PARENT_PAGE_ID], null, 1, $language);
        $homepageTitle = $pageTree[1][BackendModel::HOME_PAGE_ID]['title'] ?? \SpoonFilter::ucfirst(BL::lbl('Home'));

        foreach ($pageTree as $pageTreePages) {
            foreach ((array) $pageTreePages as $pageID => $page) {
                $parentID = (int) $page['parent_id'];

                $keys[$pageID] = trim(($keys[$parentID] ?? '') . '/' . $page['url'], '/');

                $sequences[$page['type'] === 'footer' ? 'footer' : 'pages'][$keys[$pageID]] = $pageID;

                $parentTitle = str_replace([$homepageTitle . ' → ', $homepageTitle], '', $titles[$parentID] ?? '');
                $titles[$pageID] = trim($parentTitle . ' → ' . $page['title'], ' → ');
            }
        }

        foreach ($sequences as $pageGroupSortList) {
            ksort($pageGroupSortList);

            foreach ($pageGroupSortList as $id) {
                if (isset($titles[$id])) {
                    $pages[$id] = $titles[$id];
                }
            }
        }

        return $pages;
    }

    private static function getSubTreeForDropdown(
        array $navigation,
        int $parentId,
        string $parentTitle,
        callable $attributesFunction
    ): array {
        if (!isset($navigation['page'][$parentId]) || empty($navigation['page'][$parentId])) {
            return [];
        }
        $tree = self::getEmptyTreeArray();

        foreach ($navigation['page'][$parentId] as $page) {
            $pageId = $page['page_id'];
            $pageTitle = $parentTitle . ' → ' . $page['navigation_title'];
            $tree['pages'][$pageId] = $pageTitle;
            $tree['attributes'][$pageId] = $attributesFunction($page);

            $tree = self::mergeTreeForDropdownArrays(
                $tree,
                self::getSubTreeForDropdown($navigation, $pageId, $pageTitle, $attributesFunction)
            );
        }

        return $tree;
    }

    private static function mergeTreeForDropdownArrays(array $tree, array $subTree, string $treeLabel = null): array
    {
        if (empty($subTree)) {
            return $tree;
        }

        $tree['attributes'] += $subTree['attributes'];

        if ($treeLabel === null) {
            $tree['pages'] += $subTree['pages'];

            return $tree;
        }

        $tree['pages'][$treeLabel] += $subTree['pages'];

        return $tree;
    }

    private static function getEmptyTreeArray(): array
    {
        return [
            'pages' => [],
            'attributes' => [],
        ];
    }

    private static function getAttributesFunctionForTreeName(
        string $treeName,
        string $treeLabel,
        int $currentPageId
    ): callable {
        return function (array $page) use ($treeName, $treeLabel, $currentPageId) {
            $isCurrentPage = $currentPageId === $page['page_id'];

            return [
                'data-tree-name' => $treeName,
                'data-tree-label' => $treeLabel,
                'data-allow-after' => (int) (!$isCurrentPage && $page['page_id'] !== BackendModel::HOME_PAGE_ID),
                'data-allow-inside' => (int) (!$isCurrentPage && $page['allow_children']),
                'data-allow-before' => (int) (!$isCurrentPage && $page['page_id'] !== BackendModel::HOME_PAGE_ID),
            ];
        };
    }

    private static function addMainPageToTreeForDropdown(
        array $tree,
        string $branchLabel,
        callable $attributesFunction,
        array $page,
        array $navigation
    ): array {
        $tree['pages'][$branchLabel][$page['page_id']] = $page['navigation_title'];
        $tree['attributes'][$page['page_id']] = $attributesFunction($page);

        return self::mergeTreeForDropdownArrays(
            $tree,
            self::getSubTreeForDropdown(
                $navigation,
                $page['page_id'],
                $page['navigation_title'],
                $attributesFunction
            ),
            BL::lbl('MainNavigation')
        );
    }

    public static function getMoveTreeForDropdown(int $currentPageId, string $language = null): array
    {
        $navigation = static::getCacheBuilder()->getNavigation($language = $language ?? BL::getWorkingLanguage());

        $tree = self::addMainPageToTreeForDropdown(
            self::getEmptyTreeArray(),
            BL::lbl('MainNavigation'),
            self::getAttributesFunctionForTreeName('main', BL::lbl('MainNavigation'), $currentPageId),
            $navigation['page'][0][BackendModel::HOME_PAGE_ID],
            $navigation
        );

        $treeBranches = [];
        if (BackendModel::get('fork.settings')->get('Pages', 'meta_navigation', false)) {
            $treeBranches['meta'] = BL::lbl('Meta');
        }
        $treeBranches['footer'] = BL::lbl('Footer');
        $treeBranches['root'] = BL::lbl('Root');

        foreach ($treeBranches as $branchName => $branchLabel) {
            if (!isset($navigation[$branchName][0]) || !is_array($navigation[$branchName][0])) {
                continue;
            }

            foreach ($navigation[$branchName][0] as $page) {
                $tree = self::addMainPageToTreeForDropdown(
                    $tree,
                    $branchLabel,
                    self::getAttributesFunctionForTreeName($branchName, $branchLabel, $currentPageId),
                    $page,
                    $navigation
                );
            }
        }

        return $tree;
    }

    public static function getSubtree(array $navigation, int $parentId): string
    {
        $html = '';

        // any elements
        if (isset($navigation['page'][$parentId]) && !empty($navigation['page'][$parentId])) {
            // start
            $html .= '<ul>' . "\n";

            // loop pages
            foreach ($navigation['page'][$parentId] as $page) {
                // start
                $html .= '<li id="page-' . $page['page_id'] . '" rel="' . $page['tree_type'] . '">' . "\n";

                // insert link
                $html .= '    <a href="' .
                         BackendModel::createUrlForAction(
                             'Edit',
                             null,
                             null,
                             ['id' => $page['page_id']]
                         ) . '"><ins>&#160;</ins>' . $page['navigation_title'] . '</a>' . "\n";

                // get childs
                $html .= self::getSubtree($navigation, $page['page_id']);

                // end
                $html .= '</li>' . "\n";
            }

            // end
            $html .= '</ul>' . "\n";
        }

        // return
        return $html;
    }

    /**
     * Get all pages/level
     *
     * @param int[] $ids The parentIds.
     * @param array $data A holder for the generated data.
     * @param int $level The counter for the level.
     * @param string $language The language.
     *
     * @return array
     */
    public static function getTree(array $ids, array $data = null, int $level = 1, string $language = null): array
    {
        $language = $language ?? BL::getWorkingLanguage();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $data[$level] = $pageRepository->getPageTree($ids, $language);

        // get the childIDs
        $childIds = array_keys($data[$level]);

        // build array
        if (!empty($data[$level])) {
            $data[$level] = array_map(
                function ($page) {
                    $page['has_extra'] = (bool) $page['has_extra'];
                    $page['has_children'] = (bool) $page['has_children'];

                    return $page;
                },
                $data[$level]
            );

            return self::getTree($childIds, $data, ++$level, $language);
        }

        unset($data[$level]);

        return $data;
    }

    public static function getTreeHTML(): string
    {
        $navigation = static::getCacheBuilder()->getNavigation(BL::getWorkingLanguage());

        // start HTML
        $html = '<h4>' . \SpoonFilter::ucfirst(BL::lbl('MainNavigation')) . '</h4>' . "\n";
        $html .= '<div class="clearfix" data-tree="main">' . "\n";
        $html .= '    <ul>' . "\n";
        $html .= '        <li id="page-"' . BackendModel::HOME_PAGE_ID . ' rel="home">';

        // create homepage anchor from title
        $homePage = self::get(BackendModel::HOME_PAGE_ID);
        $html .= '            <a href="' .
                 BackendModel::createUrlForAction(
                     'Edit',
                     null,
                     null,
                     ['id' => BackendModel::HOME_PAGE_ID]
                 ) . '"><ins>&#160;</ins>' . $homePage['title'] . '</a>' . "\n";

        // add subpages
        $html .= self::getSubtree($navigation, BackendModel::HOME_PAGE_ID);

        // end
        $html .= '        </li>' . "\n";
        $html .= '    </ul>' . "\n";
        $html .= '</div>' . "\n";

        // only show meta if needed
        if (BackendModel::get('fork.settings')->get('Pages', 'meta_navigation', false)) {
            // meta pages
            $html .= '<h4>' . \SpoonFilter::ucfirst(BL::lbl('Meta')) . '</h4>' . "\n";
            $html .= '<div class="clearfix" data-tree="meta">' . "\n";
            $html .= '    <ul>' . "\n";

            // are there any meta pages
            if (isset($navigation['meta'][0]) && !empty($navigation['meta'][0])) {
                // loop the items
                foreach ($navigation['meta'][0] as $page) {
                    // start
                    $html .= '        <li id="page-' . $page['page_id'] . '" rel="' . $page['tree_type'] . '">' . "\n";

                    // insert link
                    $html .= '            <a href="' .
                             BackendModel::createUrlForAction(
                                 'Edit',
                                 null,
                                 null,
                                 ['id' => $page['page_id']]
                             ) . '"><ins>&#160;</ins>' . $page['navigation_title'] . '</a>' . "\n";

                    // insert subtree
                    $html .= self::getSubtree($navigation, $page['page_id']);

                    // end
                    $html .= '        </li>' . "\n";
                }
            }

            // end
            $html .= '    </ul>' . "\n";
            $html .= '</div>' . "\n";
        }

        // footer pages
        $html .= '<h4>' . \SpoonFilter::ucfirst(BL::lbl('Footer')) . '</h4>' . "\n";

        // start
        $html .= '<div class="clearfix" data-tree="footer">' . "\n";
        $html .= '    <ul>' . "\n";

        // are there any footer pages
        if (isset($navigation['footer'][0]) && !empty($navigation['footer'][0])) {
            // loop the items
            foreach ($navigation['footer'][0] as $page) {
                // start
                $html .= '        <li id="page-' . $page['page_id'] . '" rel="' . $page['tree_type'] . '">' . "\n";

                // insert link
                $html .= '            <a href="' .
                         BackendModel::createUrlForAction(
                             'Edit',
                             null,
                             null,
                             ['id' => $page['page_id']]
                         ) . '"><ins>&#160;</ins>' . $page['navigation_title'] . '</a>' . "\n";

                // insert subtree
                $html .= self::getSubtree($navigation, $page['page_id']);

                // end
                $html .= '        </li>' . "\n";
            }
        }

        // end
        $html .= '    </ul>' . "\n";
        $html .= '</div>' . "\n";

        // are there any root pages
        if (isset($navigation['root'][0]) && !empty($navigation['root'][0])) {
            // meta pages
            $html .= '<h4>' . \SpoonFilter::ucfirst(BL::lbl('Root')) . '</h4>' . "\n";

            // start
            $html .= '<div class="clearfix" data-tree="root">' . "\n";
            $html .= '    <ul>' . "\n";

            // loop the items
            foreach ($navigation['root'][0] as $page) {
                // start
                $html .= '        <li id="page-' . $page['page_id'] . '" rel="' . $page['tree_type'] . '">' . "\n";

                // insert link
                $html .= '            <a href="' .
                         BackendModel::createUrlForAction(
                             'Edit',
                             null,
                             null,
                             ['id' => $page['page_id']]
                         ) . '"><ins>&#160;</ins>' . $page['navigation_title'] . '</a>' . "\n";

                // insert subtree
                $html .= self::getSubtree($navigation, $page['page_id']);

                // end
                $html .= '        </li>' . "\n";
            }

            // end
            $html .= '    </ul>' . "\n";
            $html .= '</div>' . "\n";
        }

        // return
        return $html;
    }

    private static function pageIsChildOfParent(array $navigation, int $childId, int $parentId): bool
    {
        if (isset($navigation['page'][$parentId]) && !empty($navigation['page'][$parentId])) {
            foreach ($navigation['page'][$parentId] as $page) {
                if ($page['page_id'] === $childId) {
                    return true;
                }

                if (self::pageIsChildOfParent($navigation, $childId, $page['page_id'])) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getTreeNameForPageId(int $pageId): ?string
    {
        $navigation = static::getCacheBuilder()->getNavigation(BL::getWorkingLanguage());

        if ($pageId === BackendModel::HOME_PAGE_ID || self::pageIsChildOfParent($navigation, $pageId, BackendModel::HOME_PAGE_ID)) {
            return 'main';
        }

        $treeNames = ['footer', 'root'];

        // only show meta if needed
        if (BackendModel::get('fork.settings')->get('Pages', 'meta_navigation', false)) {
            $treeNames[] = 'meta';
        }

        foreach ($treeNames as $treeName) {
            if (isset($navigation[$treeName][0]) && !empty($navigation[$treeName][0])) {
                // loop the items
                foreach ($navigation[$treeName][0] as $page) {
                    if ($pageId === $page['page_id'] || self::pageIsChildOfParent($navigation, $pageId, $page['page_id'])) {
                        return $treeName;
                    }
                }
            }
        }

        return null;
    }

    public static function getTypes(): array
    {
        return [
            'rich_text' => BL::lbl('Editor'),
            'block' => BL::lbl('Module'),
            'widget' => BL::lbl('Widget'),
            'usertemplate' => BL::lbl('UserTemplate'),
        ];
    }

    public static function getUrl(string $url, int $id = null, int $parentId = null, bool $isAction = false): string
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        $parentIds = [$parentId ?? self::NO_PARENT_PAGE_ID];

        // 0, 1, 2, 3, 4 are all top levels, so we should place them on the same level
        if ($parentId === self::NO_PARENT_PAGE_ID
            || $parentId === BackendModel::HOME_PAGE_ID
            || $parentId === 2
            || $parentId === 3
            || $parentId === 4
        ) {
            $parentIds = [
                self::NO_PARENT_PAGE_ID,
                BackendModel::HOME_PAGE_ID,
                2,
                3,
                4,
            ];
        }

        // no specific id
        if ($id === null) {
            $page = $pageRepository->findOneByParentsAndUrlAndStatusAndLanguage(
                $parentIds,
                $url,
                Page::ACTIVE,
                BL::getWorkingLanguage()
            );

            // no items?
            if ($page instanceof Page) {
                // add a number
                $url = BackendModel::addNumber($url);

                // recall this method, but with a new URL
                return self::getUrl($url, null, $parentId, $isAction);
            }
        } else {
            // one item should be ignored
            // there are items so, call this method again.
            $page = $pageRepository->findOneByParentsAndUrlAndStatusAndLanguageExcludingId(
                $parentIds,
                $url,
                Page::ACTIVE,
                BL::getWorkingLanguage(),
                $id
            );

            if ($page instanceof Page) {
                // add a number
                $url = BackendModel::addNumber($url);

                // recall this method, but with a new URL
                return self::getUrl($url, $id, $parentId, $isAction);
            }
        }

        // get full URL
        $fullUrl = self::getFullUrl($parentId) . '/' . $url;

        // get info about parent page
        $parentPageInfo = self::get($parentId, null, BL::getWorkingLanguage());

        // does the parent have extras?
        if (!$isAction && $parentPageInfo['has_extra']) {
            // set locale
            FrontendLanguage::setLocale(BL::getWorkingLanguage(), true);

            // get all on-site action
            $actions = FrontendLanguage::getActions();

            // if the new URL conflicts with an action we should rebuild the URL
            if (in_array($url, $actions)) {
                // add a number
                $url = BackendModel::addNumber($url);

                // recall this method, but with a new URL
                return self::getUrl($url, $id, $parentId, $isAction);
            }
        }

        // check if folder exists
        if (is_dir(PATH_WWW . '/' . $fullUrl) || is_file(PATH_WWW . '/' . $fullUrl)) {
            // add a number
            $url = BackendModel::addNumber($url);

            // recall this method, but with a new URL
            return self::getUrl($url, $id, $parentId, $isAction);
        }

        // check if it is an application
        if (array_key_exists(trim($fullUrl, '/'), ForkController::getRoutes())) {
            // add a number
            $url = BackendModel::addNumber($url);

            // recall this method, but with a new URL
            return self::getUrl($url, $id, $parentId, $isAction);
        }

        // return the unique URL!
        return $url;
    }

    /**
     * Insert multiple blocks at once
     *
     * @param array $blocks The blocks to insert.
     */
    public static function insertBlocks(array $blocks): void
    {
        if (empty($blocks)) {
            return;
        }

        /** @var PageBlockRepository $pageBlockRepository */
        $pageBlockRepository = BackendModel::get(PageBlockRepository::class);

        // loop blocks
        foreach ($blocks as $block) {
            $extraId = $block['extra_id'];
            if ($block['extra_type'] === (string) PageBlockType::userTemplate()) {
                $extraId = null;
            }

            $pageBlock = new PageBlock(
                $block['revision_id'],
                $block['position'],
                $extraId,
                new PageBlockType($block['extra_type']),
                $block['extra_data'],
                $block['html'],
                $block['visible'],
                $block['sequence']
            );

            $pageBlockRepository->add($pageBlock);
            $pageBlockRepository->save($pageBlock);
        }
    }

    public static function loadUserTemplates(): array
    {
        $themePath = FRONTEND_PATH . '/Themes/';
        $themePath .= BackendModel::get('fork.settings')->get('Core', 'theme', 'Fork');
        $filePath = $themePath . '/Core/Layout/Templates/UserTemplates/Templates.json';

        $userTemplates = [];

        $fs = new Filesystem();
        if ($fs->exists($filePath)) {
            $userTemplates = json_decode(file_get_contents($filePath), true);

            foreach ($userTemplates as &$userTemplate) {
                $userTemplate['file'] =
                    '/src/Frontend/Themes/' .
                    BackendModel::get('fork.settings')->get('Core', 'theme', 'Fork') .
                    '/Core/Layout/Templates/UserTemplates/' .
                    $userTemplate['file'];
            }
        }

        return $userTemplates;
    }

    /**
     * Move a page
     *
     * @param int $pageId The id for the page that has to be moved.
     * @param int $droppedOnPageId The id for the page where to page has been dropped on.
     * @param string $typeOfDrop The type of drop, possible values are: before, after, inside.
     * @param string $tree The tree the item is dropped on, possible values are: main, meta, footer, root.
     * @param string $language The language to use, if not provided we will use the working language.
     *
     * @return bool
     */
    public static function move(
        int $pageId,
        int $droppedOnPageId,
        string $typeOfDrop,
        string $tree,
        string $language = null
    ): bool {
        $typeOfDrop = \SpoonFilter::getValue($typeOfDrop, self::POSSIBLE_TYPES_OF_DROP, self::TYPE_OF_DROP_INSIDE);
        $tree = \SpoonFilter::getValue($tree, ['main', 'meta', 'footer', 'root'], 'root');
        $language = $language ?? BL::getWorkingLanguage();

        // When dropping on the main navigation it should be added as a child of the home page
        if ($tree === 'main' && $droppedOnPageId === 0) {
            $droppedOnPageId = BackendModel::HOME_PAGE_ID;
            $typeOfDrop = self::TYPE_OF_DROP_INSIDE;
        }

        // reset type of drop for special pages
        if ($droppedOnPageId === BackendModel::HOME_PAGE_ID || $droppedOnPageId === self::NO_PARENT_PAGE_ID) {
            $typeOfDrop = self::TYPE_OF_DROP_INSIDE;
        }

        $page = self::get($pageId, null, $language);
        $droppedOnPage = self::get(
            ($droppedOnPageId === self::NO_PARENT_PAGE_ID ? BackendModel::HOME_PAGE_ID : $droppedOnPageId),
            null,
            $language
        );

        if (empty($page) || empty($droppedOnPage)) {
            return false;
        }

        try {
            $newParent = self::getNewParent($droppedOnPageId, $typeOfDrop, $droppedOnPage);
        } catch (InvalidArgumentException $invalidArgumentException) {
            // parent doesn't allow children
            return false;
        }

        self::recalculateSequenceAfterMove(
            $typeOfDrop,
            self::getNewType($droppedOnPageId, $tree, $newParent, $droppedOnPage),
            $pageId,
            $language,
            $newParent,
            $droppedOnPage['id']
        );

        self::updateUrlAfterMove($pageId, $page, $newParent);

        return true;
    }

    public static function update(array $page): int
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        /** @var MetaRepository $metaRepository */
        $metaRepository = BackendModel::get('fork.repository.meta');

        if (self::isForbiddenToDelete($page['id'])) {
            $page['allow_delete'] = false;
        }

        if (self::isForbiddenToMove($page['id'])) {
            $page['allow_move'] = false;
        }

        if (self::isForbiddenToHaveChildren($page['id'])) {
            $page['allow_children'] = false;
        }

        // update old revisions
        if ($page['status'] !== Page::DRAFT) {
            $pageEntities = $pageRepository->findBy(['id' => $page['id'], 'language' => $page['language']]);

            foreach ($pageEntities as $pageEntity) {
                $pageEntity->archive();
                $pageRepository->save($pageEntity);
            }
        } else {
            $pageRepository->deleteByIdAndUserIdAndStatusAndLanguage(
                (int) $page['id'],
                BackendAuthentication::getUser()->getUserId(),
                Page::DRAFT,
                $page['language']
            );
        }

        $meta = $metaRepository->find($page['meta_id']);

        $pageEntity = new Page(
            $page['id'],
            $page['user_id'],
            $page['parent_id'],
            $page['template_id'],
            $meta,
            $page['language'],
            $page['title'],
            $page['navigation_title'],
            new DateTime($page['publish_on']),
            $page['sequence'],
            $page['navigation_title_overwrite'],
            $page['hidden'],
            new Status($page['status']),
            $page['type'],
            $page['data'],
            $page['allow_move'],
            $page['allow_children'],
            $page['allow_edit'],
            $page['allow_delete']
        );

        $pageRepository->add($pageEntity);
        $pageRepository->save($pageEntity);

        // how many revisions should we keep
        $rowsToKeep = (int) BackendModel::get('fork.settings')->get('Pages', 'max_num_revisions', 20);

        // get revision-ids for items to keep
        $revisionIdsToKeep = $pageRepository->getRevisionIdsToKeep($page['id'], $rowsToKeep);

        // delete other revisions
        if (count($revisionIdsToKeep) !== 0) {
            // because blocks are linked by revision we should get all revisions we want to delete

            $revisionsToDelete = $pageRepository
                ->getRevisionIdsToDelete(
                    $page['id'],
                    Page::ARCHIVE,
                    $revisionIdsToKeep
                );

            // any revisions to delete
            if (count($revisionsToDelete) !== 0) {
                $pageRepository->deleteByRevisionIds($revisionsToDelete);

                /** @var PageBlockRepository $pageBlockRepository */
                $pageBlockRepository = BackendModel::get(PageBlockRepository::class);
                $pageBlockRepository->deleteByRevisionIds($revisionsToDelete);
            }
        }

        // return the new revision id
        return $pageEntity->getRevisionId();
    }

    /**
     * Switch templates for all existing pages
     *
     * @param int $oldTemplateId The id of the new template to replace.
     * @param int $newTemplateId The id of the new template to use.
     * @param bool $overwrite Overwrite all pages with default blocks.
     */
    public static function updatePagesTemplates(int $oldTemplateId, int $newTemplateId, bool $overwrite = false): void
    {
        // fetch new template data
        $newTemplate = BackendExtensionsModel::getTemplate($newTemplateId);
        $newTemplate['data'] = @unserialize($newTemplate['data']);

        // fetch all pages
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $pages = $pageRepository->findBy(['templateId' => $oldTemplateId, 'status' => [Status::active(), Status::draft()]]);

        // there is no active/draft page with the old template id
        if (count($pages) === 0) {
            return;
        }

        // loop pages
        /** @var Page $page */
        foreach ($pages as $page) {
            // fetch blocks
            $blocksContent = self::getBlocks($page->getId(), $page->getRevisionId(), $page->getLanguage());

            // save new page revision
            $newPageRevisionId = self::update(
                [
                    'id' => $page->getId(),
                    'user_id' => $page->getUserId(),
                    'parent_id' => $page->getParentId(),
                    'template_id' => $newTemplateId,
                    'meta_id' => $page->getMeta()->getId(),
                    'language' => $page->getLanguage(),
                    'title' => $page->getTitle(),
                    'navigation_title' => $page->getNavigationTitle(),
                    'publish_on' => $page->getPublishOn()->format('Y-m-d H:i:s'),
                    'sequence' => $page->getSequence(),
                    'navigation_title_overwrite' => $page->isNavigationTitleOverwrite(),
                    'hidden' => $page->isHidden(),
                    'status' => $page->getStatus(),
                    'type' => $page->getType(),
                    'data' => $page->getData(),
                    'allow_move' => $page->isAllowMove(),
                    'allow_children' => $page->isAllowChildren(),
                    'allow_edit' => $page->isAllowEdit(),
                    'allow_delete' => $page->isAllowDelete(),
                ]
            );

            // overwrite all blocks with current defaults
            if ($overwrite) {
                $blocksContent = [];

                // fetch default blocks for this page
                $defaultBlocks = [];
                if (isset($newTemplate['data']['default_extras_' . $page->getLanguage()])) {
                    $defaultBlocks = $newTemplate['data']['default_extras_' . $page->getLanguage()];
                } elseif (isset($newTemplate['data']['default_extras'])) {
                    $defaultBlocks = $newTemplate['data']['default_extras'];
                }

                // loop positions
                foreach ($defaultBlocks as $position => $blocks) {
                    // loop blocks
                    foreach ($blocks as $extraId) {
                        // add to the list
                        $blocksContent[] = [
                            'revision_id' => $newPageRevisionId,
                            'position' => $position,
                            'extra_id' => $extraId,
                            'extra_type' => 'rich_text',
                            'html' => '',
                            'created_on' => BackendModel::getUTCDate(),
                            'edited_on' => BackendModel::getUTCDate(),
                            'visible' => true,
                            'sequence' => count($defaultBlocks[$position]) - 1,
                        ];
                    }
                }
            } else {
                // don't overwrite blocks, just re-use existing
                // set new page revision id
                foreach ($blocksContent as &$block) {
                    $block['revision_id'] = $newPageRevisionId;
                    $block['created_on'] = BackendModel::getUTCDate(null, $block['created_on']);
                    $block['edited_on'] = BackendModel::getUTCDate(null, $block['edited_on']);
                }
            }

            // insert the blocks
            self::insertBlocks($blocksContent);
        }
    }

    public static function getEncodedRedirectUrl(string $redirectUrl): string
    {
        preg_match('!(http[s]?)://(.*)!i', $redirectUrl, $matches);
        $urlChunks = explode('/', $matches[2]);
        if (!empty($urlChunks)) {
            // skip domain name
            $domain = array_shift($urlChunks);
            foreach ($urlChunks as &$urlChunk) {
                $urlChunk = rawurlencode($urlChunk);
            }
            unset($urlChunk);
            $redirectUrl = $matches[1] . '://' . $domain . '/' . implode('/', $urlChunks);
        }

        return $redirectUrl;
    }

    private static function getNewParent(int $droppedOnPageId, string $typeOfDrop, array $droppedOnPage): int
    {
        if ($droppedOnPageId === self::NO_PARENT_PAGE_ID) {
            return self::NO_PARENT_PAGE_ID;
        }

        if ($typeOfDrop === self::TYPE_OF_DROP_INSIDE) {
            // check if item allows children
            if (!$droppedOnPage['allow_children']) {
                throw new InvalidArgumentException('Parent page is not allowed to have child pages');
            }

            return $droppedOnPage['id'];
        }

        // if the item has to be moved before or after
        return $droppedOnPage['parent_id'];
    }

    private static function getNewType(int $droppedOnPageId, string $tree, int $newParent, array $droppedOnPage): string
    {
        if ($droppedOnPageId === self::NO_PARENT_PAGE_ID) {
            if ($tree === 'footer') {
                return 'footer';
            }

            if ($tree === 'meta') {
                return 'meta';
            }

            return 'root';
        }

        if ($newParent === self::NO_PARENT_PAGE_ID) {
            return $droppedOnPage['type'];
        }

        return 'page';
    }

    private static function recalculateSequenceAfterMove(
        string $typeOfDrop,
        string $newType,
        int $pageId,
        string $language,
        string $newParent,
        int $droppedOnPageId
    ): void {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        // calculate new sequence for items that should be moved inside
        if ($typeOfDrop === self::TYPE_OF_DROP_INSIDE) {
            $newSequence = $pageRepository->getNewSequenceForMove($newParent, $language);

            $pages = $pageRepository->findBy(['id' => $pageId, 'language' => $language, 'status' => Status::active()]);

            foreach ($pages as $page) {
                $page->move($newParent, $newSequence, $newType);
                $pageRepository->save($page);
            }

            return;
        }

        $droppedOnPage = $pageRepository
            ->findOneBy(
                [
                    'id' => $droppedOnPageId,
                    'language' => $language,
                    'status' => Page::ACTIVE,
                ]
            );

        if (!$droppedOnPage instanceof Page) {
            throw new RuntimeException('Drop on page not found');
        }

        // calculate new sequence for items that should be moved before or after
        $droppedOnPageSequence = $droppedOnPage->getSequence();

        $newSequence = $droppedOnPageSequence + ($typeOfDrop === self::TYPE_OF_DROP_BEFORE ? -1 : 1);

        // increment all pages with a sequence that is higher than the new sequence;
        $pageRepository->incrementSequence($newParent, $language, $newSequence);

        $pages = $pageRepository->findBy(['id' => $pageId, 'language' => $language, 'status' => Page::ACTIVE]);

        foreach ($pages as $page) {
            $page->move($newParent, $newSequence, $newType);
            $pageRepository->save($page);
        }
    }

    private static function updateUrlAfterMove(int $pageId, array $page, int $newParent): void
    {
        /** @var MetaRepository $metaRepository */
        $metaRepository = BackendModel::get('fork.repository.meta');
        $meta = $metaRepository->find($page['meta_id']);

        if (!$meta instanceof Meta) {
            return;
        }

        $newUrl = self::getUrl(
            $meta->getUrl(),
            $pageId,
            $newParent,
            isset($page['data']['is_action']) && $page['data']['is_action']
        );

        $meta->update(
            $meta->getKeywords(),
            $meta->isKeywordsOverwrite(),
            $meta->getDescription(),
            $meta->isDescriptionOverwrite(),
            $meta->getTitle(),
            $meta->isTitleOverwrite(),
            $newUrl,
            $meta->isUrlOverwrite()
        );

        $metaRepository->save($meta);
    }
}
