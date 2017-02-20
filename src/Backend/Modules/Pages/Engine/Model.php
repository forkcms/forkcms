<?php

namespace Backend\Modules\Pages\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\Command\CopyContentBlocksToOtherLocale;
use Backend\Modules\ContentBlocks\Engine\Model as BackendContentBlocksModel;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use ForkCMS\App\ForkController;
use Frontend\Core\Language\Language as FrontendLanguage;

/**
 * In this file we store all generic functions that we will be using in the PagesModule
 */
class Model
{
    const QRY_BROWSE_RECENT =
        'SELECT i.id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM pages AS i
         WHERE i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC
         LIMIT ?';

    const QRY_DATAGRID_BROWSE_DRAFTS =
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

    const QRY_BROWSE_REVISIONS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM pages AS i
         WHERE i.id = ? AND i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    const QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM pages AS i
         WHERE i.id = ? AND i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    const QRY_BROWSE_TEMPLATES =
        'SELECT i.id, i.label AS title
         FROM pages_templates AS i
         WHERE i.theme = ?
         ORDER BY i.label ASC';

    /**
     * @return CacheBuilder
     */
    public static function getCacheBuilder()
    {
        static $cacheBuilder = null;
        if ($cacheBuilder === null) {
            $cacheBuilder = new CacheBuilder(BackendModel::get('database'), BackendModel::get('cache.pool'));
        }

        return $cacheBuilder;
    }

    /**
     * Build the cache
     *
     * @param string $language The language to build the cache for, if not passed we use the working language.
     */
    public static function buildCache($language = null)
    {
        // redefine
        $language = ($language === null) ? BL::getWorkingLanguage() : (string) $language;

        $cacheBuilder = static::getCacheBuilder();
        $cacheBuilder->buildCache($language);
    }

    /**
     * Copy pages
     *
     * @param string $from The language code to copy the pages from.
     * @param string $to   The language code we want to copy the pages to.
     */
    public static function copy($from, $to)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // copy contentBlocks and get copied contentBlockIds
        $copyContentBlocks = new CopyContentBlocksToOtherLocale(Locale::fromString($to), Locale::fromString($from));
        BackendModel::get('command_bus')->handle($copyContentBlocks);
        $contentBlockIds = $copyContentBlocks->extraIdMap;

        // define old block ids
        $contentBlockOldIds = array_keys($contentBlockIds);

        // get all old pages
        $ids = $db->getColumn(
            'SELECT id
             FROM pages AS i
             WHERE i.language = ? AND i.status = ?',
            array($to, 'active')
        );

        // any old pages
        if (!empty($ids)) {
            // delete existing pages
            foreach ($ids as $id) {
                // redefine
                $id = (int) $id;

                // get revision ids
                $revisionIDs = (array) $db->getColumn(
                    'SELECT i.revision_id
                     FROM pages AS i
                     WHERE i.id = ? AND i.language = ?',
                    array($id, $to)
                );

                // get meta ids
                $metaIDs = (array) $db->getColumn(
                    'SELECT i.meta_id
                     FROM pages AS i
                     WHERE i.id = ? AND i.language = ?',
                    array($id, $to)
                );

                // delete meta records
                if (!empty($metaIDs)) {
                    $db->delete('meta', 'id IN (' . implode(',', $metaIDs) . ')');
                }

                // delete blocks and their revisions
                if (!empty($revisionIDs)) {
                    $db->delete(
                        'pages_blocks',
                        'revision_id IN (' . implode(',', $revisionIDs) . ')'
                    );
                }

                // delete page and the revisions
                if (!empty($revisionIDs)) {
                    $db->delete('pages', 'revision_id IN (' . implode(',', $revisionIDs) . ')');
                }
            }
        }

        // delete search indexes
        $db->delete('search_index', 'module = ? AND language = ?', array('pages', $to));

        // get all active pages
        $ids = BackendModel::getContainer()->get('database')->getColumn(
            'SELECT id
             FROM pages AS i
             WHERE i.language = ? AND i.status = ?',
            array($from, 'active')
        );

        // loop
        foreach ($ids as $id) {
            // get data
            $sourceData = self::get($id, null, $from);

            // get and build meta
            $meta = $db->getRecord(
                'SELECT *
                 FROM meta
                 WHERE id = ?',
                array($sourceData['meta_id'])
            );

            // remove id
            unset($meta['id']);

            // init page
            $page = array();

            // build page
            $page['id'] = $sourceData['id'];
            $page['user_id'] = BackendAuthentication::getUser()->getUserId();
            $page['parent_id'] = $sourceData['parent_id'];
            $page['template_id'] = $sourceData['template_id'];
            $page['meta_id'] = (int) $db->insert('meta', $meta);
            $page['language'] = $to;
            $page['type'] = $sourceData['type'];
            $page['title'] = $sourceData['title'];
            $page['navigation_title'] = $sourceData['navigation_title'];
            $page['navigation_title_overwrite'] = $sourceData['navigation_title_overwrite'];
            $page['hidden'] = $sourceData['hidden'];
            $page['status'] = 'active';
            $page['publish_on'] = BackendModel::getUTCDate();
            $page['created_on'] = BackendModel::getUTCDate();
            $page['edited_on'] = BackendModel::getUTCDate();
            $page['allow_move'] = $sourceData['allow_move'];
            $page['allow_children'] = $sourceData['allow_children'];
            $page['allow_edit'] = $sourceData['allow_edit'];
            $page['allow_delete'] = $sourceData['allow_delete'];
            $page['sequence'] = $sourceData['sequence'];
            $page['data'] = ($sourceData['data'] !== null) ? serialize($sourceData['data']) : null;

            // insert page, store the id, we need it when building the blocks
            $revisionId = self::insert($page);

            // init var
            $blocks = array();

            // get the blocks
            $sourceBlocks = self::getBlocks($id, null, $from);

            // loop blocks
            foreach ($sourceBlocks as $sourceBlock) {
                // build block
                $block = $sourceBlock;
                $block['revision_id'] = $revisionId;
                $block['created_on'] = BackendModel::getUTCDate();
                $block['edited_on'] = BackendModel::getUTCDate();

                if (in_array($block['extra_id'], $contentBlockOldIds)) {
                    $block['extra_id'] = $contentBlockIds[$block['extra_id']];
                }

                // add block
                $blocks[] = $block;
            }

            // insert the blocks
            self::insertBlocks($blocks);

            // init var
            $text = '';

            // build search-text
            foreach ($blocks as $block) {
                $text .= ' ' . $block['html'];
            }

            // add
            BackendSearchModel::saveIndex(
                'Pages',
                (int) $page['id'],
                array('title' => $page['title'], 'text' => $text),
                $to
            );

            // get tags
            $tags = BackendTagsModel::getTags('pages', $id, 'string', $from);

            // save tags
            if ($tags != '') {
                $saveWorkingLanguage = BL::getWorkingLanguage();

                // If we don't set the working language to the target language,
                // BackendTagsModel::getURL() will use the current working
                // language, possibly causing unnecessary '-2' suffixes in
                // tags.url
                BL::setWorkingLanguage($to);

                BackendTagsModel::saveTags($page['id'], $tags, 'pages', $to);
                BL::setWorkingLanguage($saveWorkingLanguage);
            }
        }

        // build cache
        self::buildCache($to);
    }

    /**
     * Creates the html for the menu
     *
     * @param string $type     The type of navigation.
     * @param int    $depth    The maximum depth to show.
     * @param int    $parentId The Id to start from.
     * @param string $html     Will hold the created HTML.
     *
     * @return string
     */
    public static function createHtml($type = 'page', $depth = 0, $parentId = 1, $html = '')
    {
        $navigation = static::getCacheBuilder()->getNavigation(BL::getWorkingLanguage());

        // check if item exists
        if (isset($navigation[$type][$depth][$parentId])) {
            // start html
            $html .= '<ul>' . "\n";

            // loop elements
            foreach ($navigation[$type][$depth][$parentId] as $key => $aValue) {
                $html .= "\t<li>" . "\n";
                $html .= "\t\t" . '<a href="#">' . $aValue['navigation_title'] . '</a>' . "\n";

                // insert recursive here!
                if (isset($navigation[$type][$depth + 1][$key])) {
                    $html .= self::createHtml(
                        $type,
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
     * Delete a page
     *
     * @param int    $id         The id of the page to delete.
     * @param string $language   The language wherein the page will be deleted,
     *                           if not provided we will use the working language.
     * @param int    $revisionId If specified the given revision will be deleted, used for deleting drafts.
     *
     * @return bool
     */
    public static function delete($id, $language = null, $revisionId = null)
    {
        // redefine
        $id = (int) $id;
        $language = ($language === null) ? BL::getWorkingLanguage() : (string) $language;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // get record
        $page = self::get($id, $revisionId, $language);

        // validate
        if (empty($page)) {
            return false;
        }
        if ($page['allow_delete'] == 'N') {
            return false;
        }

        // get revision ids
        $revisionIDs = (array) $db->getColumn(
            'SELECT i.revision_id
             FROM pages AS i
             WHERE i.id = ? AND i.language = ?',
            array($id, $language)
        );

        // get meta ids
        $metaIDs = (array) $db->getColumn(
            'SELECT i.meta_id
             FROM pages AS i
             WHERE i.id = ? AND i.language = ?',
            array($id, $language)
        );

        // delete meta records
        if (!empty($metaIDs)) {
            $db->delete('meta', 'id IN (' . implode(',', $metaIDs) . ')');
        }

        // delete blocks and their revisions
        if (!empty($revisionIDs)) {
            $db->delete('pages_blocks', 'revision_id IN (' . implode(',', $revisionIDs) . ')');
        }

        // delete page and the revisions
        if (!empty($revisionIDs)) {
            $db->delete('pages', 'revision_id IN (' . implode(',', $revisionIDs) . ')');
        }

        // delete tags
        BackendTagsModel::saveTags($id, '', 'Pages');

        // return
        return true;
    }

    /**
     * Check if a page exists
     *
     * @param int $id The id to check for existence.
     *
     * @return bool
     */
    public static function exists($id)
    {
        // redefine
        $id = (int) $id;
        $language = BL::getWorkingLanguage();

        // exists?
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM pages AS i
             WHERE i.id = ? AND i.language = ? AND i.status IN (?, ?)
             LIMIT 1',
            array($id, $language, 'active', 'draft')
        );
    }

    /**
     * Get the data for a record
     *
     * @param int    $id       The Id of the page to fetch.
     * @param int    $revisionId
     * @param string $language The language to use while fetching the page.
     *
     * @return mixed False if the record can't be found, otherwise an array with all data.
     */
    public static function get($id, $revisionId = null, $language = null)
    {
        // fetch revision if not specified
        if ($revisionId === null) {
            $revisionId = self::getLatestRevision($id, $language);
        }

        // redefine
        $id = (int) $id;
        $revisionId = (int) $revisionId;
        $language = ($language === null) ? BL::getWorkingLanguage() : (string) $language;

        // get page (active version)
        $return = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on,
                UNIX_TIMESTAMP(i.edited_on) AS edited_on,
             IF(COUNT(e.id) > 0, "Y", "N") AS has_extra,
             GROUP_CONCAT(b.extra_id) AS extra_ids
             FROM pages AS i
             LEFT OUTER JOIN pages_blocks AS b ON b.revision_id = i.revision_id AND b.extra_id IS NOT NULL
             LEFT OUTER JOIN modules_extras AS e ON e.id = b.extra_id AND e.type = ?
             WHERE i.id = ? AND i.revision_id = ? AND i.language = ?
             GROUP BY i.revision_id',
            array('block', $id, $revisionId, $language)
        );

        // no page?
        if (empty($return)) {
            return false;
        }

        // can't be deleted
        if (in_array($return['id'], array(1, 404))) {
            $return['allow_delete'] = 'N';
        }

        // can't be moved
        if (in_array($return['id'], array(1, 404))) {
            $return['allow_move'] = 'N';
        }

        // can't have children
        if (in_array($return['id'], array(404))) {
            $return['allow_move'] = 'N';
        }

        // convert into bools for use in template engine
        $return['move_allowed'] = (bool) ($return['allow_move'] == 'Y');
        $return['children_allowed'] = (bool) ($return['allow_children'] == 'Y');
        $return['edit_allowed'] = (bool) ($return['allow_edit'] == 'Y');
        $return['delete_allowed'] = (bool) ($return['allow_delete'] == 'Y');

        // unserialize data
        if ($return['data'] !== null) {
            $return['data'] = unserialize($return['data']);
        }

        // return
        return $return;
    }

    /**
     * Get blocks for a certain page/revision
     *
     * @param int    $id         The id of the page.
     * @param int    $revisionId The revision to grab.
     * @param string $language   The language to use.
     *
     * @return array
     */
    public static function getBlocks($id, $revisionId = null, $language = null)
    {
        // fetch revision if not specified
        if ($revisionId === null) {
            $revisionId = self::getLatestRevision($id, $language);
        }

        // redefine
        $id = (int) $id;
        $revisionId = (int) $revisionId;
        $language = ($language === null) ? BL::getWorkingLanguage() : (string) $language;

        // get page (active version)
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT b.*, UNIX_TIMESTAMP(b.created_on) AS created_on, UNIX_TIMESTAMP(b.edited_on) AS edited_on
             FROM pages_blocks AS b
             INNER JOIN pages AS i ON b.revision_id = i.revision_id
                WHERE i.id = ? AND i.revision_id = ? AND i.language = ?
                ORDER BY b.sequence ASC',
            array($id, $revisionId, $language)
        );
    }

    /**
     * Get all items by a given tag id
     *
     * @param int $tagId The id of the tag.
     *
     * @return array
     */
    public static function getByTag($tagId)
    {
        // redefine
        $tagId = (int) $tagId;

        // get the items
        $items = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS url, i.title AS name, mt.module
             FROM modules_tags AS mt
             INNER JOIN tags AS t ON mt.tag_id = t.id
             INNER JOIN pages AS i ON mt.other_id = i.id
             WHERE mt.module = ? AND mt.tag_id = ? AND i.status = ?',
            array('pages', $tagId, 'active')
        );

        // loop items
        foreach ($items as &$row) {
            $row['url'] = BackendModel::createURLForAction(
                'Edit',
                'Pages',
                null,
                array('id' => $row['url'])
            );
        }

        // return
        return $items;
    }

    /**
     * Get the first child for a given parent
     *
     * @param int $pageId The Id of the page to get the first child for.
     *
     * @return mixed
     */
    public static function getFirstChildId($pageId)
    {
        // redefine
        $pageId = (int) $pageId;

        // get child
        $childId = (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM pages AS i
             WHERE i.parent_id = ? AND i.status = ? AND i.language = ?
             ORDER BY i.sequence ASC
             LIMIT 1',
            array($pageId, 'active', BL::getWorkingLanguage())
        );

        // return
        if ($childId != 0) {
            return $childId;
        }

        // fallback
        return false;
    }

    /**
     * Get the full-URL for a given menuId
     *
     * @param int $id The Id of the page to get the URL for.
     *
     * @return string
     */
    public static function getFullURL($id)
    {
        $keys = static::getCacheBuilder()->getKeys(BL::getWorkingLanguage());
        $hasMultiLanguages = BackendModel::getContainer()->getParameter('site.multilanguage');

        // available in generated file?
        if (isset($keys[$id])) {
            $url = $keys[$id];
        } elseif ($id == 0) {
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

    /**
     * Get latest revision id for a page.
     *
     * @param int    $id       The id of the page.
     * @param string $language The language to use.
     *
     * @return int
     */
    public static function getLatestRevision($id, $language = null)
    {
        // redefine
        $id = (int) $id;
        $language = ($language === null) ? BL::getWorkingLanguage() : (string) $language;

        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT revision_id
             FROM pages AS i
             WHERE i.id = ? AND i.language = ? AND i.status != ?',
            array($id, $language, 'archive')
        );
    }

    /**
     * Get the maximum unique id for blocks
     *
     * @return int
     */
    public static function getMaximumBlockId()
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.id) FROM pages_blocks AS i'
        );
    }

    /**
     * Get the maximum unique id for pages
     *
     * @param string $language The language to use, if not provided we will use the working language.
     *
     * @return int
     */
    public static function getMaximumPageId($language = null)
    {
        // redefine
        $language = ($language !== null) ? (string) $language : BL::getWorkingLanguage();

        // get the maximum id
        $maximumMenuId = (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.id) FROM pages AS i WHERE i.language = ?',
            array($language)
        );

        // pages created by a user that isn't a god should have an id higher then 1000
        // with this hack we can easily find which pages are added by a user
        if ($maximumMenuId < 1000 && !BackendAuthentication::getUser()->isGod()) {
            return $maximumMenuId + 1000;
        }

        // fallback
        return $maximumMenuId;
    }

    /**
     * Get the maximum sequence inside a leaf
     *
     * @param int    $parentId The Id of the parent.
     * @param string $language The language to use, if not provided we will use the working language.
     *
     * @return int
     */
    public static function getMaximumSequence($parentId, $language = null)
    {
        $parentId = (int) $parentId;
        $language = ($language !== null) ? (string) $language : BL::getWorkingLanguage();

        // get the maximum sequence inside a certain leaf
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM pages AS i
             WHERE i.language = ? AND i.parent_id = ?',
            array($language, $parentId)
        );
    }

    /**
     * Get the pages for usage in a dropdown menu
     *
     * @param string $language The language to use, if not provided we will use the working language.
     *
     * @return array
     */
    public static function getPagesForDropdown($language = null)
    {
        // redefine
        $language = ($language !== null) ? (string) $language : BL::getWorkingLanguage();

        // get tree
        $levels = self::getTree(array(0), null, 1, $language);

        // init var
        $titles = array();
        $sequences = array();
        $keys = array();
        $return = array();

        // loop levels
        foreach ($levels as $pages) {
            // loop all items on this level
            foreach ($pages as $pageID => $page) {
                // init var
                $parentID = (int) $page['parent_id'];

                // get URL for parent
                $url = (isset($keys[$parentID])) ? $keys[$parentID] : '';

                // add it
                $keys[$pageID] = trim($url . '/' . $page['url'], '/');

                // add to sequences
                if ($page['type'] == 'footer') {
                    $sequences['footer'][(string) trim(
                        $url . '/' . $page['url'],
                        '/'
                    )] = $pageID;
                } else {
                    $sequences['pages'][(string) trim($url . '/' . $page['url'], '/')] = $pageID;
                }

                // get URL for parent
                $title = (isset($titles[$parentID])) ? $titles[$parentID] : '';
                $title = trim($title, \SpoonFilter::ucfirst(BL::lbl('Home')) . ' > ');

                // add it
                $titles[$pageID] = trim($title . ' > ' . $page['title'], ' > ');
            }
        }

        if (isset($sequences['pages'])) {
            // sort the sequences
            ksort($sequences['pages']);

            // loop to add the titles in the correct order
            foreach ($sequences['pages'] as $id) {
                if (isset($titles[$id])) {
                    $return[$id] = $titles[$id];
                }
            }
        }

        if (isset($sequences['footer'])) {
            foreach ($sequences['footer'] as $id) {
                if (isset($titles[$id])) {
                    $return[$id] = $titles[$id];
                }
            }
        }

        // return
        return $return;
    }

    /**
     * Get the subtree for a root element
     *
     * @param array  $navigation The navigation array.
     * @param int    $parentId   The id of the parent.
     *
     * @return string
     */
    public static function getSubtree($navigation, $parentId)
    {
        $navigation = (array) $navigation;
        $parentId = (int) $parentId;
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
                         BackendModel::createURLForAction(
                             'Edit',
                             null,
                             null,
                             array('id' => $page['page_id'])
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
     * @param integer[]  $ids      The parentIds.
     * @param array  $data     A holder for the generated data.
     * @param int    $level    The counter for the level.
     * @param string $language The language.
     *
     * @return array
     */
    public static function getTree(array $ids, array $data = null, $level = 1, $language = null)
    {
        // redefine
        $level = (int) $level;
        $language = ($language !== null) ? (string) $language : BL::getWorkingLanguage();

        // get data
        $data[$level] = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT
                 i.id, i.title, i.parent_id, i.navigation_title, i.type, i.hidden, i.data,
                m.url, m.data AS meta_data,
                IF(COUNT(e.id) > 0, "Y", "N") AS has_extra,
                GROUP_CONCAT(b.extra_id) AS extra_ids,
                IF(COUNT(p.id), "Y", "N") AS has_children
             FROM pages AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             LEFT OUTER JOIN pages_blocks AS b ON b.revision_id = i.revision_id
             LEFT OUTER JOIN modules_extras AS e ON e.id = b.extra_id AND e.type = ?
             LEFT OUTER JOIN pages AS p ON p.parent_id = i.id AND p.status = "active" AND p.hidden = "N"
             AND p.language = i.language
             WHERE i.parent_id IN (' . implode(', ', $ids) . ')
                 AND i.status = ? AND i.language = ?
             GROUP BY i.revision_id
             ORDER BY i.sequence ASC',
            array('block', 'active', $language),
            'id'
        );

        // get the childIDs
        $childIds = array_keys($data[$level]);

        // build array
        if (!empty($data[$level])) {
            return self::getTree($childIds, $data, ++$level, $language);
        } else {
            // cleanup
            unset($data[$level]);
        }

        // return
        return $data;
    }

    /**
     * Get the tree
     *
     * @return string
     */
    public static function getTreeHTML()
    {
        $navigation = static::getCacheBuilder()->getNavigation(BL::getWorkingLanguage());

        // start HTML
        $html = '<h4>' . \SpoonFilter::ucfirst(BL::lbl('MainNavigation')) . '</h4>' . "\n";
        $html .= '<div class="clearfix" data-tree="main">' . "\n";
        $html .= '    <ul>' . "\n";
        $html .= '        <li id="page-1" rel="home">';

        // create homepage anchor from title
        $homePage = self::get(1);
        $html .= '            <a href="' .
                 BackendModel::createURLForAction(
                     'Edit',
                     null,
                     null,
                     array('id' => 1)
                 ) . '"><ins>&#160;</ins>' . $homePage['title'] . '</a>' . "\n";

        // add subpages
        $html .= self::getSubtree($navigation, 1);

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
                             BackendModel::createURLForAction(
                                 'Edit',
                                 null,
                                 null,
                                 array('id' => $page['page_id'])
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
                         BackendModel::createURLForAction(
                             'Edit',
                             null,
                             null,
                             array('id' => $page['page_id'])
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
                         BackendModel::createURLForAction(
                             'Edit',
                             null,
                             null,
                             array('id' => $page['page_id'])
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

    /**
     * Get the possible block types
     *
     * @return array
     */
    public static function getTypes()
    {
        return array(
            'rich_text' => BL::lbl('Editor'),
            'block' => BL::lbl('Module'),
            'widget' => BL::lbl('Widget'),
        );
    }

    /**
     * Get an unique URL for a page
     *
     * @param string $url      The URL to base on.
     * @param int    $id       The id to ignore.
     * @param int    $parentId The parent for the page to create an url for.
     * @param bool   $isAction Is this page an action.
     *
     * @return string
     */
    public static function getURL($url, $id = null, $parentId = 0, $isAction = false)
    {
        $url = (string) $url;
        $parentIds = array((int) $parentId);

        // 0, 1, 2, 3, 4 are all top levels, so we should place them on the same level
        if ($parentId == 0 || $parentId == 1 || $parentId == 2 || $parentId == 3 || $parentId == 4) {
            $parentIds = array(
                0,
                1,
                2,
                3,
                4,
            );
        }

        // get db
        $db = BackendModel::getContainer()->get('database');

        // no specific id
        if ($id === null) {
            // no items?
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM pages AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.parent_id IN(' . implode(',', $parentIds) . ') AND i.status = ? AND m.url = ?
                    AND i.language = ?
                 LIMIT 1',
                array('active', $url, BL::getWorkingLanguage())
            )
            ) {
                // add a number
                $url = BackendModel::addNumber($url);

                // recall this method, but with a new URL
                return self::getURL($url, null, $parentId, $isAction);
            }
        } else {
            // one item should be ignored
            // there are items so, call this method again.
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM pages AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.parent_id IN(' . implode(',', $parentIds) . ') AND i.status = ?
                    AND m.url = ? AND i.id != ? AND i.language = ?
                 LIMIT 1',
                array('active', $url, $id, BL::getWorkingLanguage())
            )
            ) {
                // add a number
                $url = BackendModel::addNumber($url);

                // recall this method, but with a new URL
                return self::getURL($url, $id, $parentId, $isAction);
            }
        }

        // get full URL
        $fullURL = self::getFullURL($parentId) . '/' . $url;

        // get info about parent page
        $parentPageInfo = self::get($parentId, null, BL::getWorkingLanguage());

        // does the parent have extras?
        if ($parentPageInfo['has_extra'] == 'Y' && !$isAction) {
            // set locale
            FrontendLanguage::setLocale(BL::getWorkingLanguage(), true);

            // get all on-site action
            $actions = FrontendLanguage::getActions();

            // if the new URL conflicts with an action we should rebuild the URL
            if (in_array($url, $actions)) {
                // add a number
                $url = BackendModel::addNumber($url);

                // recall this method, but with a new URL
                return self::getURL($url, $id, $parentId, $isAction);
            }
        }

        // check if folder exists
        if (is_dir(PATH_WWW . '/' . $fullURL) || is_file(PATH_WWW . '/' . $fullURL)) {
            // add a number
            $url = BackendModel::addNumber($url);

            // recall this method, but with a new URL
            return self::getURL($url, $id, $parentId, $isAction);
        }

        // check if it is an application
        if (array_key_exists(trim($fullURL, '/'), ForkController::getRoutes())) {
            // add a number
            $url = BackendModel::addNumber($url);

            // recall this method, but with a new URL
            return self::getURL($url, $id, $parentId, $isAction);
        }

        // return the unique URL!
        return $url;
    }

    /**
     * Insert a page
     *
     * @param array $page The data for the page.
     *
     * @return int
     */
    public static function insert(array $page)
    {
        return (int) BackendModel::getContainer()->get('database')->insert('pages', $page);
    }

    /**
     * Insert multiple blocks at once
     *
     * @param array $blocks The blocks to insert.
     */
    public static function insertBlocks(array $blocks)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // loop blocks
        foreach ($blocks as $block) {
            // insert blocks
            $db->insert('pages_blocks', $block);
        }
    }

    /**
     * Move a page
     *
     * @param int    $id         The id for the page that has to be moved.
     * @param int    $droppedOn  The id for the page where to page has been dropped on.
     * @param string $typeOfDrop The type of drop, possible values are: before, after, inside.
     * @param string $tree       The tree the item is dropped on, possible values are: main, meta, footer, root.
     * @param string $language   The language to use, if not provided we will use the working language.
     *
     * @return bool
     */
    public static function move($id, $droppedOn, $typeOfDrop, $tree, $language = null)
    {
        $id = (int) $id;
        $droppedOn = (int) $droppedOn;
        $typeOfDrop = \SpoonFilter::getValue($typeOfDrop, array('before', 'after', 'inside'), 'inside');
        $tree = \SpoonFilter::getValue($tree, array('main', 'meta', 'footer', 'root'), 'inside');
        $language = ($language === null) ? BL::getWorkingLanguage() : (string) $language;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // reset type of drop for special pages
        if ($droppedOn == 1) {
            $typeOfDrop = 'inside';
        }
        if ($droppedOn == 0) {
            $typeOfDrop = 'inside';
        }

        // get data for pages
        $page = self::get($id, null, $language);
        $droppedOnPage = self::get($droppedOn, null, $language);

        // reset if the drop was on 0 (new meta)
        if ($droppedOn == 0) {
            $droppedOnPage = self::get(1, null, $language);
        }

        // validate
        if (empty($page) || empty($droppedOnPage)) {
            return false;
        }

        // calculate new parent for items that should be moved inside
        if ($droppedOn == 0) {
            $newParent = 0;
        } elseif ($typeOfDrop == 'inside') {
            // check if item allows children
            if ($droppedOnPage['allow_children'] != 'Y') {
                return false;
            }

            // set new parent to the dropped on page.
            $newParent = $droppedOnPage['id'];
        } else {
            // if the item has to be moved before or after
            $newParent = $droppedOnPage['parent_id'];
        }

        // decide new type
        if ($droppedOn == 0) {
            if ($tree == 'footer') {
                $newType = 'footer';
            } else {
                $newType = 'meta';
            }
        } elseif ($newParent == 0) {
            $newType = $droppedOnPage['type'];
        } else {
            $newType = 'page';
        }

        // calculate new sequence for items that should be moved inside
        if ($typeOfDrop == 'inside') {
            // get highest sequence + 1
            $newSequence = (int) $db->getVar(
                'SELECT MAX(i.sequence)
                 FROM pages AS i
                 WHERE i.id = ? AND i.language = ? AND i.status = ?',
                array($newParent, $language, 'active')
            ) + 1;

            // update
            $db->update(
                'pages',
                array('parent_id' => $newParent, 'sequence' => $newSequence, 'type' => $newType),
                'id = ? AND language = ? AND status = ?',
                array($id, $language, 'active')
            );
        } elseif ($typeOfDrop == 'before') {
            // calculate new sequence for items that should be moved before
            // get new sequence
            $newSequence = (int) $db->getVar(
                'SELECT i.sequence
                 FROM pages AS i
                 WHERE i.id = ? AND i.language = ? AND i.status = ?
                 LIMIT 1',
                array($droppedOnPage['id'], $language, 'active')
            ) - 1;

            // increment all pages with a sequence that is higher or equal to the current sequence;
            $db->execute(
                'UPDATE pages
                 SET sequence = sequence + 1
                 WHERE parent_id = ? AND language = ? AND sequence >= ?',
                array($newParent, $language, $newSequence + 1)
            );

            // update
            $db->update(
                'pages',
                array('parent_id' => $newParent, 'sequence' => $newSequence, 'type' => $newType),
                'id = ? AND language = ? AND status = ?',
                array($id, $language, 'active')
            );
        } elseif ($typeOfDrop == 'after') {
            // calculate new sequence for items that should be moved after
            // get new sequence
            $newSequence = (int) $db->getVar(
                'SELECT i.sequence
                FROM pages AS i
                WHERE i.id = ? AND i.language = ? AND i.status = ?
                LIMIT 1',
                array($droppedOnPage['id'], $language, 'active')
            ) + 1;

            // increment all pages with a sequence that is higher then the current sequence;
            $db->execute(
                'UPDATE pages
                 SET sequence = sequence + 1
                 WHERE parent_id = ? AND language = ? AND sequence > ?',
                array($newParent, $language, $newSequence)
            );

            // update
            $db->update(
                'pages',
                array('parent_id' => $newParent, 'sequence' => $newSequence, 'type' => $newType),
                'id = ? AND language = ? AND status = ?',
                array($id, $language, 'active')
            );
        } else {
            return false;
        }

        // get current URL
        $currentURL = (string) $db->getVar(
            'SELECT url
             FROM meta AS m
             WHERE m.id = ?',
            array($page['meta_id'])
        );

        // rebuild url
        $newURL = self::getURL(
            $currentURL,
            $id,
            $newParent,
            (isset($page['data']['is_action']) && $page['data']['is_action'] == 'Y')
        );

        // store
        $db->update('meta', array('url' => $newURL), 'id = ?', array($page['meta_id']));

        // return
        return true;
    }

    /**
     * Update a page
     *
     * @param array $page The new data for the page.
     *
     * @return int
     */
    public static function update(array $page)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // update old revisions
        if ($page['status'] != 'draft') {
            $db->update(
                'pages',
                array('status' => 'archive'),
                'id = ? AND language = ?',
                array((int) $page['id'], $page['language'])
            );
        } else {
            $db->delete(
                'pages',
                'id = ? AND user_id = ? AND status = ? AND language = ?',
                array((int) $page['id'], BackendAuthentication::getUser()->getUserId(), 'draft', $page['language'])
            );
        }

        // insert
        $page['revision_id'] = (int) $db->insert('pages', $page);

        // how many revisions should we keep
        $rowsToKeep = (int) BackendModel::get('fork.settings')->get('Pages', 'max_num_revisions', 20);

        // get revision-ids for items to keep
        $revisionIdsToKeep = (array) $db->getColumn(
            'SELECT i.revision_id
             FROM pages AS i
             WHERE i.id = ? AND i.status = ?
             ORDER BY i.edited_on DESC
             LIMIT ?',
            array((int) $page['id'], 'archive', $rowsToKeep)
        );

        // delete other revisions
        if (!empty($revisionIdsToKeep)) {
            // because blocks are linked by revision we should get all revisions we want to delete
            $revisionsToDelete = (array) $db->getColumn(
                'SELECT i.revision_id
                 FROM pages AS i
                 WHERE i.id = ? AND i.status = ? AND i.revision_id NOT IN(' . implode(', ', $revisionIdsToKeep) . ')',
                array((int) $page['id'], 'archive')
            );

            // any revisions to delete
            if (!empty($revisionsToDelete)) {
                $db->delete('pages', 'revision_id IN(' . implode(', ', $revisionsToDelete) . ')');
                $db->delete('pages_blocks', 'revision_id IN(' . implode(', ', $revisionsToDelete) . ')');
            }
        }

        // return the new revision id
        return $page['revision_id'];
    }

    /**
     * Switch templates for all existing pages
     *
     * @param int  $oldTemplateId The id of the new template to replace.
     * @param int  $newTemplateId The id of the new template to use.
     * @param bool $overwrite     Overwrite all pages with default blocks.
     */
    public static function updatePagesTemplates($oldTemplateId, $newTemplateId, $overwrite = false)
    {
        $newTemplateId = (int) $newTemplateId;
        $oldTemplateId = (int) $oldTemplateId;
        $overwrite = (bool) $overwrite;

        // fetch new template data
        $newTemplate = BackendExtensionsModel::getTemplate($newTemplateId);
        $newTemplate['data'] = @unserialize($newTemplate['data']);

        // fetch all pages
        $pages = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT *
             FROM pages
             WHERE template_id = ? AND status IN (?, ?)',
            array($oldTemplateId, 'active', 'draft')
        );

        // there is no active/draft page with the old template id
        if (empty($pages)) {
            return;
        }

        // loop pages
        foreach ($pages as $page) {
            // fetch blocks
            $blocksContent = self::getBlocks($page['id'], $page['revision_id'], $page['language']);

            // unset revision id
            unset($page['revision_id']);

            // change template
            $page['template_id'] = $newTemplateId;

            // save new page revision
            $page['revision_id'] = self::update($page);

            // overwrite all blocks with current defaults
            if ($overwrite) {
                // init var
                $blocksContent = array();

                // fetch default blocks for this page
                $defaultBlocks = array();
                if (isset($newTemplate['data']['default_extras_' . $page['language']])) {
                    $defaultBlocks = $newTemplate['data']['default_extras_' . $page['language']];
                } elseif (isset($newTemplate['data']['default_extras'])) {
                    $defaultBlocks = $newTemplate['data']['default_extras'];
                }

                // loop positions
                foreach ($defaultBlocks as $position => $blocks) {
                    // loop blocks
                    foreach ($blocks as $extraId) {
                        // build block
                        $block = array();
                        $block['revision_id'] = $page['revision_id'];
                        $block['position'] = $position;
                        $block['extra_id'] = $extraId;
                        $block['html'] = '';
                        $block['created_on'] = BackendModel::getUTCDate();
                        $block['edited_on'] = $block['created_on'];
                        $block['visible'] = 'Y';
                        $block['sequence'] = count($defaultBlocks[$position]) - 1;

                        // add to the list
                        $blocksContent[] = $block;
                    }
                }
            } else {
                // don't overwrite blocks, just re-use existing
                // set new page revision id
                foreach ($blocksContent as &$block) {
                    $block['revision_id'] = $page['revision_id'];
                    $block['created_on'] = BackendModel::getUTCDate(null, $block['created_on']);
                    $block['edited_on'] = BackendModel::getUTCDate(null, $block['edited_on']);
                }
            }

            // insert the blocks
            self::insertBlocks($blocksContent);
        }
    }

    /**
     * Get encoded redirect URL
     *
     * @param string $redirectURL
     *
     * @return string
     */
    public static function getEncodedRedirectURL($redirectURL)
    {
        preg_match('!(http[s]?)://(.*)!i', $redirectURL, $matches);
        $URLChunks = explode('/', $matches[2]);
        if (!empty($URLChunks)) {
            // skip domain name
            $domain = array_shift($URLChunks);
            foreach ($URLChunks as &$URLChunk) {
                $URLChunk = rawurlencode($URLChunk);
            }
            $redirectURL = $matches[1] . '://' . $domain . '/' . implode('/', $URLChunks);
        }

        return $redirectURL;
    }
}
