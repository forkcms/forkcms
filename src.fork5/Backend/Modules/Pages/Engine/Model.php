<?php

namespace Backend\Modules\Pages\Engine;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BL;
use Backend\Core\Language\Locale;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\Page\Status;
use Backend\Modules\Pages\Domain\Page\Type;
use Backend\Modules\Pages\Domain\PageBlock\PageBlock;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use Backend\Modules\Pages\Domain\PageBlock\Type as PageBlockType;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Common\Doctrine\Entity\Meta;
use Common\Doctrine\Repository\MetaRepository;
use Common\Locale as AbstractLocale;
use InvalidArgumentException;
use RuntimeException;
use SpoonFilter;
use Symfony\Component\Filesystem\Filesystem;

/**
 * In this file we store all generic functions that we will be using in the PagesModule
 */
class Model
{
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
         FROM PagesPage AS i
         WHERE i.status = ? AND i.locale = ?
         ORDER BY i.edited_on DESC
         LIMIT ?';

    const QUERY_DATAGRID_BROWSE_DRAFTS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM PagesPage AS i
         INNER JOIN
         (
             SELECT MAX(i.revision_id) AS revision_id
             FROM PagesPage AS i
             WHERE i.status = ? AND i.user_id = ? AND i.locale = ?
             GROUP BY i.id
         ) AS p
         WHERE i.revision_id = p.revision_id';

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

    public static function buildCache(AbstractLocale $locale = null): void
    {
        $cacheBuilder = static::getCacheBuilder();
        $cacheBuilder->buildCache($locale ?? Locale::workingLocale());
    }

    /**
     * @param int $id The id of the page to delete.
     * @param Locale $locale The locale wherein the page will be deleted,
     *                           if not provided we will use the working locale.
     * @param int $revisionId If specified the given revision will be deleted, used for deleting drafts.
     *
     * @return bool
     */
    public static function delete(int $id, Locale $locale = null, int $revisionId = null): bool
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        /** @var MetaRepository $metaRepository */
        $metaRepository = BackendModel::get(MetaRepository::class);

        $locale = $locale ?? Locale::workingLocale();

        // get record
        $page = self::get($id, $revisionId, $locale);

        // validate
        if (empty($page)) {
            return false;
        }
        if (!$page['allow_delete']) {
            return false;
        }

        // get revision ids
        $pages = $pageRepository->findBy(['id' => $id, 'locale' => $locale]);

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
                'locale' => Locale::workingLocale(),
                'status' => [Status::active(), Status::draft()],
            ]
        );

        return $page instanceof Page;
    }

    /**
     * Get the data for a record
     *
     * @param int $pageId The Id of the page to fetch.
     * @param int $revisionId
     * @param Locale $locale The locale to use while fetching the page.
     *
     * @return mixed False if the record can't be found, otherwise an array with all data.
     */
    public static function get(int $pageId, int $revisionId = null, Locale $locale = null)
    {
        // fetch revision if not specified
        if ($revisionId === null) {
            $revisionId = self::getLatestRevision($pageId, $locale);
        }

        // redefine
        $locale = $locale ?? Locale::workingLocale();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $page = $pageRepository->getOne($pageId, $revisionId, $locale);

        // no page?
        if ($page === null) {
            return false;
        }

        return $page;
    }

    public static function isForbiddenToDelete(int $pageId): bool
    {
        return in_array($pageId, [Page::HOME_PAGE_ID, Page::ERROR_PAGE_ID], true);
    }

    public static function isForbiddenToMove(int $pageId): bool
    {
        return in_array($pageId, [Page::HOME_PAGE_ID, Page::ERROR_PAGE_ID], true);
    }

    public static function isForbiddenToHaveChildren(int $pageId): bool
    {
        return $pageId === Page::ERROR_PAGE_ID;
    }

    public static function getBlocks(int $pageId, int $revisionId = null, Locale $locale = null): array
    {
        // fetch revision if not specified
        if ($revisionId === null) {
            $revisionId = self::getLatestRevision($pageId, $locale);
        }

        // redefine
        $locale = $locale ?? Locale::workingLocale();

        /** @var PageBlockRepository $pageBlockRepository */
        $pageBlockRepository = BackendModel::get(PageBlockRepository::class);

        return $pageBlockRepository->getBlocksForPage($pageId, $revisionId, $locale);
    }

    public static function getByTag(int $tagId): array
    {
        // get the items
        $items = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS url, i.title AS name, mt.moduleName AS module
             FROM TagsModuleTag AS mt
             INNER JOIN TagsTag AS t ON mt.tag_id = t.id AND mt.moduleName = ? AND mt.tag_id = ?
             INNER JOIN PagesPage AS i ON mt.moduleId = i.id AND i.status = ? AND i.locale = ?',
            ['pages', $tagId, 'active', BL::getWorkingLanguage()]
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
        $page = $pageRepository->getFirstChild($parentId, Status::active(), Locale::workingLocale());

        if ($page instanceof Page) {
            return $page->getId();
        }

        return null;
    }

    public static function getFullUrl(int $id): string
    {
        $keys = static::getCacheBuilder()->getKeys(Locale::workingLocale());
        $hasMultiLanguages = BackendModel::getContainer()->getParameter('site.multilanguage');

        // available in generated file?
        if (isset($keys[$id])) {
            $url = $keys[$id];
        } elseif ($id == Page::NO_PARENT_PAGE_ID) {
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

    public static function getLatestRevision(int $id, Locale $locale = null): int
    {
        $locale = $locale ?? Locale::workingLocale();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        return (int) $pageRepository->getLatestVersion($id, $locale);
    }

    public static function getMaximumPageId(Locale $locale = null): int
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        return $pageRepository->getMaximumPageId(
            $locale ?? Locale::workingLocale(),
            BackendAuthentication::getUser()->isGod()
        );
    }

    public static function getMaximumSequence(int $parentId, Locale $locale = null): int
    {
        $locale = $locale ?? Locale::workingLocale();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        // get the maximum sequence inside a certain leaf
        return $pageRepository->getMaximumSequence($parentId, $locale);
    }

    public static function getPagesForDropdown(Locale $locale = null): array
    {
        $locale = $locale ?? Locale::workingLocale();
        $titles = [];
        $sequences = [
            'pages' => [],
            'footer' => [],
        ];
        $keys = [];
        $pages = [];
        $pageTree = self::getTree([Page::NO_PARENT_PAGE_ID], null, 1, $locale);
        $homepageTitle = htmlentities($pageTree[1][Page::HOME_PAGE_ID]['title'] ?? SpoonFilter::ucfirst(BL::lbl('Home')));

        foreach ($pageTree as $pageTreePages) {
            foreach ((array) $pageTreePages as $pageID => $page) {
                $parentID = (int) $page['parent_id'];

                $keys[$pageID] = trim(($keys[$parentID] ?? '') . '/' . $page['url'], '/');

                $sequences[$page['type'] === 'footer' ? 'footer' : 'pages'][$keys[$pageID]] = $pageID;

                $parentTitle = str_replace([$homepageTitle . ' → ', $homepageTitle], '', $titles[$parentID] ?? '');
                $titles[$pageID] = htmlspecialchars(trim($parentTitle . ' → ' . $page['title'], ' → '));
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
            $pageTitle = htmlspecialchars($parentTitle . ' → ' . $page['navigation_title']);
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
                'data-allow-after' => (int) (!$isCurrentPage && $page['page_id'] !== Page::HOME_PAGE_ID),
                'data-allow-inside' => (int) (!$isCurrentPage && $page['allow_children']),
                'data-allow-before' => (int) (!$isCurrentPage && $page['page_id'] !== Page::HOME_PAGE_ID),
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
        $tree['pages'][$branchLabel][$page['page_id']] = SpoonFilter::htmlentities($page['navigation_title']);
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

    public static function getMoveTreeForDropdown(int $currentPageId, Locale $locale = null): array
    {
        $navigation = static::getCacheBuilder()->getNavigation($locale ?? Locale::workingLocale());

        $tree = self::addMainPageToTreeForDropdown(
            self::getEmptyTreeArray(),
            BL::lbl('MainNavigation'),
            self::getAttributesFunctionForTreeName('main', BL::lbl('MainNavigation'), $currentPageId),
            $navigation['page'][0][Page::HOME_PAGE_ID],
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

    private static function getSubtree(Type $type, array $navigation, int $parentId): ?array
    {
        $subPages = $navigation[(string) $type][$parentId] ?? null;

        if ($subPages === null || count($subPages) === 0) {
            return null;
        }

        $subTree = [];
        foreach ($subPages as $page) {
            $subTree[$page['page_id']] = [
                'attr' => [
                    'rel' => $page['tree_type'],
                    'data-jstree' => '{"type":"' . $page['tree_type'] . '"}',
                ],
                'page' => $page,
                'children' => self::getSubtree(Type::page(), $navigation, $page['page_id']),
            ];
        }

        return $subTree;
    }

    /**
     * Get all pages/level
     *
     * @param int[] $ids The parentIds.
     * @param array $data A holder for the generated data.
     * @param int $level The counter for the level.
     * @param AbstractLocale $locale
     *
     * @return array
     */
    public static function getTree(array $ids, array $data = null, int $level = 1, AbstractLocale $locale = null): array
    {
        $locale = $locale ?? Locale::workingLocale();

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $data[$level] = $pageRepository->getPageTree($ids, $locale);

        // get the childIDs
        $childIds = array_keys($data[$level]);

        // build array
        if (!empty($data[$level])) {
            $data[$level] = array_map(
                function ($page) {
                    $page['has_extra'] = (bool) $page['has_extra'];
                    $page['has_children'] = (bool) $page['has_children'];
                    $page['allow_move'] = (bool) $page['allow_move'];

                    return $page;
                },
                $data[$level]
            );

            return self::getTree($childIds, $data, ++$level, $locale);
        }

        unset($data[$level]);

        return $data;
    }

    public static function getTreeHTML(): string
    {
        $navigation = static::getCacheBuilder()->getNavigation(Locale::workingLocale());

        $tree = [];

        $tree['main'] = [
            'name' => 'main',
            'label' => 'MainNavigation',
            'pages' => self::getSubtree(Type::page(), $navigation, 0),
        ];
        if (BackendModel::get('fork.settings')->get('Pages', 'meta_navigation', false)) {
            $tree['meta'] = [
                'name' => 'meta',
                'label' => 'Meta',
                'pages' => self::getSubtree(Type::meta(), $navigation, 0),
            ];
        }
        $tree['footer'] = [
            'name' => 'footer',
            'label' => 'Footer',
            'pages' => self::getSubtree(Type::footer(), $navigation, 0),
        ];
        $tree['root'] = [
            'name' => 'root',
            'label' => 'Root',
            'pages' => self::getSubtree(Type::root(), $navigation, 0),
        ];

        // return
        return BackendModel::getContainer()->get('templating')->render(
            BACKEND_MODULES_PATH . '/Pages/Resources/views/NavigationTree.html.twig',
            [
                'editUrl' => BackendModel::createUrlForAction('PageEdit', 'Pages'),
                'tree' => $tree,
            ]
        );
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
        $navigation = static::getCacheBuilder()->getNavigation(Locale::workingLocale());

        if ($pageId === Page::HOME_PAGE_ID || self::pageIsChildOfParent($navigation, $pageId, Page::HOME_PAGE_ID)) {
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
                    if ($pageId === $page['page_id']
                        || self::pageIsChildOfParent($navigation, $pageId, $page['page_id'])) {
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

    /**
     * @deprecated use the repository
     */
    public static function getUrl(string $url, int $id = null, int $parentId = null, bool $isAction = false): string
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        return $pageRepository->getUrl($url, Locale::workingLocale(), $id, $parentId, $isAction);
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

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        // loop blocks
        foreach ($blocks as $block) {
            $extraId = $block['extra_id'];

            if (!isset($block['page']) && isset($block['revision_id'])) {
                $block['page'] = $pageRepository->find($block['revision_id']);
            }

            $pageBlock = new PageBlock(
                $block['page'],
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
     * @param Locale $locale The locale to use, if not provided we will use the working locale.
     *
     * @return bool
     */
    public static function move(
        int $pageId,
        int $droppedOnPageId,
        string $typeOfDrop,
        string $tree,
        Locale $locale = null
    ): bool {
        $typeOfDrop = SpoonFilter::getValue($typeOfDrop, self::POSSIBLE_TYPES_OF_DROP, self::TYPE_OF_DROP_INSIDE);
        $tree = SpoonFilter::getValue($tree, ['main', 'meta', 'footer', 'root'], 'root');
        $locale = $locale ?? Locale::workingLocale();

        // When dropping on the main navigation it should be added as a child of the home page
        if ($tree === 'main' && $droppedOnPageId === 0) {
            $droppedOnPageId = Page::HOME_PAGE_ID;
            $typeOfDrop = self::TYPE_OF_DROP_INSIDE;
        }

        // reset type of drop for special pages
        if ($droppedOnPageId === Page::HOME_PAGE_ID || $droppedOnPageId === Page::NO_PARENT_PAGE_ID) {
            $typeOfDrop = self::TYPE_OF_DROP_INSIDE;
        }

        $page = self::get($pageId, null, $locale);
        $droppedOnPage = self::get(
            ($droppedOnPageId === Page::NO_PARENT_PAGE_ID ? Page::HOME_PAGE_ID : $droppedOnPageId),
            null,
            $locale
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
            $locale,
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
        $metaRepository = BackendModel::get(MetaRepository::class);

        $locale = Locale::fromString($page['locale']);

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
        if ($page['status'] !== (string) Status::draft()) {
            $pageEntities = $pageRepository->findBy(
                [
                    'id' => $page['id'],
                    'locale' => $locale,
                ]
            );

            foreach ($pageEntities as $pageEntity) {
                $pageEntity->archive();
                $pageRepository->save($pageEntity);
            }
        } else {
            $pageRepository->deleteByIdAndUserIdAndStatusAndLocale(
                (int) $page['id'],
                BackendAuthentication::getUser()->getUserId(),
                Status::draft(),
                $locale
            );
        }

        $meta = $metaRepository->find($page['meta_id']);

        $pageEntity = new Page(
            $page['id'],
            $page['user_id'],
            $page['parent_id'],
            $page['template_id'],
            clone $meta,
            $locale,
            $page['title'],
            $page['navigation_title'],
            null,
            $page['publish_on'],
            null,
            $page['sequence'],
            $page['navigation_title_overwrite'],
            $page['hidden'],
            new Status($page['status']),
            new Type($page['type']),
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
                    Status::archive(),
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
     * @param array $page
     */
    public static function updateRevisionData(int $pageId, int $revisionId, array $data): void
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $page = $pageRepository->findOneBy(['id' => $pageId, 'revisionId' => $revisionId]);
        $pageDataTransferObject = new PageDataTransferObject($page);
        $pageDataTransferObject->data = $data;
        $pageRepository->save(Page::fromDataTransferObject($pageDataTransferObject));
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
        $newTemplate['data'] = @unserialize($newTemplate['data'], ['allowed_classes' => false]);

        // fetch all pages
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);
        $pages = $pageRepository->findBy(
            ['templateId' => $oldTemplateId, 'status' => [Status::active(), Status::draft()]]
        );

        // there is no active/draft page with the old template id
        if (count($pages) === 0) {
            return;
        }

        // loop pages
        /** @var Page $page */
        foreach ($pages as $page) {
            // fetch blocks
            $blocksContent = self::getBlocks($page->getId(), $page->getRevisionId(), $page->getLocale());

            // save new page revision
            $newPageRevisionId = self::update(
                [
                    'id' => $page->getId(),
                    'user_id' => $page->getUserId(),
                    'parent_id' => $page->getParentId(),
                    'template_id' => $newTemplateId,
                    'meta_id' => $page->getMeta()->getId(),
                    'locale' => $page->getLocale(),
                    'title' => $page->getTitle(),
                    'navigation_title' => $page->getNavigationTitle(),
                    'publish_on' => $page->getPublishOn(),
                    'publish_until' => $page->getPublishUntil(),
                    'sequence' => $page->getSequence(),
                    'navigation_title_overwrite' => $page->isNavigationTitleOverwrite(),
                    'hidden' => $page->isHidden(),
                    'status' => (string) $page->getStatus(),
                    'type' => (string) $page->getType(),
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
                if (isset($newTemplate['data']['default_extras_' . $page->getLocale()])) {
                    $defaultBlocks = $newTemplate['data']['default_extras_' . $page->getLocale()];
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
                    $block['created_on'] = BackendModel::getUTCDate(null, $block['created_on']->getTimestamp());
                    $block['edited_on'] = BackendModel::getUTCDate(null, $block['edited_on']->getTimestamp());
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
        if ($droppedOnPageId === Page::NO_PARENT_PAGE_ID) {
            return Page::NO_PARENT_PAGE_ID;
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

    private static function getNewType(int $droppedOnPageId, string $tree, int $newParent, array $droppedOnPage): Type
    {
        if ($droppedOnPageId === Page::NO_PARENT_PAGE_ID) {
            if ($tree === 'footer') {
                return Type::footer();
            }

            if ($tree === 'meta') {
                return Type::meta();
            }

            return Type::root();
        }

        if ($newParent === Page::NO_PARENT_PAGE_ID) {
            return $droppedOnPage['type'];
        }

        return Type::page();
    }

    private static function recalculateSequenceAfterMove(
        string $typeOfDrop,
        Type $newType,
        int $pageId,
        Locale $locale,
        string $newParent,
        int $droppedOnPageId
    ): void {
        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::get(PageRepository::class);

        // calculate new sequence for items that should be moved inside
        if ($typeOfDrop === self::TYPE_OF_DROP_INSIDE) {
            $newSequence = $pageRepository->getNewSequenceForMove((int) $newParent, $locale);

            $pages = $pageRepository->findBy(['id' => $pageId, 'locale' => $locale, 'status' => Status::active()]);

            foreach ($pages as $page) {
                $page->move((int) $newParent, $newSequence, $newType);
                $pageRepository->save($page);
            }

            return;
        }

        $droppedOnPage = $pageRepository
            ->findOneBy(
                [
                    'id' => $droppedOnPageId,
                    'locale' => $locale,
                    'status' => Status::active(),
                ]
            );

        if (!$droppedOnPage instanceof Page) {
            throw new RuntimeException('Drop on page not found');
        }

        // calculate new sequence for items that should be moved before or after
        $droppedOnPageSequence = $droppedOnPage->getSequence();

        $newSequence = $droppedOnPageSequence + ($typeOfDrop === self::TYPE_OF_DROP_BEFORE ? -1 : 1);

        // increment all pages with a sequence that is higher than the new sequence;
        $pageRepository->incrementSequence($newParent, $locale, $newSequence);

        $pages = $pageRepository->findBy(['id' => $pageId, 'locale' => $locale, 'status' => Status::active()]);

        foreach ($pages as $page) {
            $page->move((int) $newParent, $newSequence, $newType);
            $pageRepository->save($page);
        }
    }

    private static function updateUrlAfterMove(int $pageId, array $page, int $newParent): void
    {
        /** @var MetaRepository $metaRepository */
        $metaRepository = BackendModel::get(MetaRepository::class);
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
