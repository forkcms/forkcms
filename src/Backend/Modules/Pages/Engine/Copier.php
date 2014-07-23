<?php

namespace Backend\Modules\Pages\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\ContentBlocks\Engine\Copier as ContentBlocksCopier;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This class copies pages from one language to another
 *
 * @author Wouter Sioen <wouter@wijs.be>
 */
class Copier
{
    /**
     * @var \SpoonDatabase
     */
    protected $database;

    /**
     * @var string
     */
    private $from;
    private $to;
    private $copyDate;

    /**
     * @var int
     */
    private $fromSite;
    private $toSite;

    public function __construct(\SpoonDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * Copy pages
     *
     * @param string $from     The language code to copy the pages from.
     * @param string $to       The language code we want to copy the pages to.
     * @param int    $fromSite The site_id code to copy the pages from.
     * @param int    $toSite   The site_id code we want to copy the pages to.
     */
    public function copy($from, $to, $fromSite, $toSite)
    {
        $this->from = $from;
        $this->to = $to;
        $this->fromSite = $fromSite;
        $this->toSite = $toSite;

        $this->copyDate = BackendModel::getUTCDate();

        $contentBlockIds = $this->copyContentBlocks();
        $contentBlockOldIds = array_keys($contentBlockIds);

        $this->deleteOldPages();
        $this->deleteSearchIndices();

        $this->copyPages($contentBlockIds, $contentBlockOldIds);

        // build cache
        Model::buildCache($this->to, $this->toSite);
    }

    /**
     * Runs the ContentBlocksCopier to fetch new content block ids
     *
     * @return array keys = old content blocks, values = new content blocks
     */
    protected function copyContentBlocks()
    {
        // copy contentBlocks and get copied contentBlockIds
        $blocksCopier = new ContentBlocksCopier($this->database);
        return $blocksCopier->copy(
            $this->from,
            $this->to,
            $this->fromSite,
            $this->toSite
        );
    }

    /**
     * Removes all old pages for the site/language we're copying to
     */
    protected function deleteOldPages()
    {
        // get all old pages
        $ids = $this->database->getColumn(
            'SELECT id
             FROM pages AS i
             WHERE i.language = ? AND i.site_id = ? AND i.status = ?',
            array($this->to, $this->toSite, 'active')
        );

        // delete them
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $id = (int) $id;

                // get revision ids
                $revisionIDs = (array) $this->database->getColumn(
                    'SELECT i.revision_id
                     FROM pages AS i
                     WHERE i.id = ? AND i.language = ? AND i.site_id = ?',
                    array($id, $this->to, $this->toSite)
                );

                // get meta ids
                $metaIDs = (array) $this->database->getColumn(
                    'SELECT i.meta_id
                     FROM pages AS i
                     WHERE i.id = ? AND i.language = ? AND i.site_id = ?',
                    array($id, $this->to, $this->toSite)
                );

                // delete meta records
                if (!empty($metaIDs)) {
                    $this->database->delete(
                        'meta',
                        'id IN (' . implode(',', $metaIDs) . ')'
                    );
                }

                // delete blocks and their revisions
                if (!empty($revisionIDs)) {
                    $this->database->delete(
                        'pages_blocks',
                        'revision_id IN (' . implode(',', $revisionIDs) . ')'
                    );
                }

                // delete page and the revisions
                if (!empty($revisionIDs)) {
                    $this->database->delete(
                        'pages',
                        'revision_id IN (' . implode(',', $revisionIDs) . ')'
                    );
                }
            }
        }
    }

    /**
     * Removes search indices for the site/language we're copying to
     */
    protected function deleteSearchIndices()
    {
        // delete search indexes
        $this->database->delete(
            'search_index',
            'module = ? AND language = ? AND site_id = ?',
            array('pages', $this->to, $this->toSite)
        );
    }

    /**
     * This function copies all pages from the old to the new site/language
     *
     * @param array $contentBlockIds    The ids of the new content blocks
     * @param array $contentBlockOldIds The ids of the old content blocks
     */
    protected function copyPages($contentBlockIds, $contentBlockOldIds)
    {
        // get all active pages
        $ids = $this->database->getColumn(
            'SELECT id
             FROM pages AS i
             WHERE i.language = ? AND i.status = ?',
            array($this->from, 'active')
        );

        foreach ($ids as $id) {
            $sourceData = Model::get($id, null, $this->from);

            $page = $this->copyPage($sourceData);
            $blocks = $this->copyPageBlocks($sourceData, $page, $contentBlockIds, $contentBlockOldIds);
            $this->saveSearchIndex($blocks, $page);
            $this->copyPageTags($id, $page);
        }
    }

    /**
     * Copies one page
     *
     * @param array $sourceData
     * @return array info about the old page
     */
    protected function copyPage($sourceData)
    {
        // get and build meta
        $meta = $this->database->getRecord(
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
        $page['meta_id'] = (int) $this->database->insert('meta', $meta);
        $page['language'] = $this->to;
        $page['site_id'] = $this->toSite;
        $page['type'] = $sourceData['type'];
        $page['title'] = $sourceData['title'];
        $page['navigation_title'] = $sourceData['navigation_title'];
        $page['navigation_title_overwrite'] = $sourceData['navigation_title_overwrite'];
        $page['hidden'] = $sourceData['hidden'];
        $page['status'] = 'active';
        $page['publish_on'] = $this->copyDate;
        $page['created_on'] = $this->copyDate;
        $page['edited_on'] = $this->copyDate;
        $page['allow_move'] = $sourceData['allow_move'];
        $page['allow_children'] = $sourceData['allow_children'];
        $page['allow_edit'] = $sourceData['allow_edit'];
        $page['allow_delete'] = $sourceData['allow_delete'];
        $page['sequence'] = $sourceData['sequence'];
        $page['data'] = ($sourceData['data'] !== null) ? serialize($sourceData['data']) : null;

        // insert page, store the id, we need it when building the blocks
        $page['revision_id'] = Model::insert($page);

        return $page;
    }

    /**
     * Copies all blocks for a new page
     *
     * @param array $sourceData         Info for the old page
     * @param array $page               Info about the new page
     * @param array $contentBlockIds    The ids of the new content blocks
     * @param array $contentBlockOldIds The ids of the old content blocks
     * @return array The new blocks.
     */
    protected function copyPageBlocks($sourceData, $page, $contentBlockIds, $contentBlockOldIds)
    {
        // init var
        $blocks = array();
        $hasBlock = ($sourceData['has_extra'] == 'Y');

        // get the blocks
        $sourceBlocks = Model::getBlocks($sourceData['id'], null, $this->from);

        // loop blocks
        foreach ($sourceBlocks as $sourceBlock) {
            // build block
            $block = $sourceBlock;
            $block['revision_id'] = $page['revision_id'];
            $block['created_on'] = $this->copyDate;
            $block['edited_on'] = $this->copyDate;

            if (in_array($block['extra_id'], $contentBlockOldIds)) {
                $block['extra_id'] = $contentBlockIds[$block['extra_id']];
            }

            // add block
            $blocks[] = $block;
        }

        // insert the blocks
        Model::insertBlocks($blocks, $hasBlock);

        return $blocks;
    }

    /**
     * Creates a search index for a new page
     *
     * @param array $blocks The blocks on the page
     * @param array $page   Info about our new page
     */
    protected function saveSearchIndex($blocks, $page)
    {
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
            $this->to
        );
    }

    /**
     * Copies all tags for a page
     *
     * @param int   $id The id of the page
     * @param array $page Info about our new page
     */
    protected function copyPageTags($id, $page)
    {
        $tags = BackendTagsModel::getTags('pages', $id, 'string', $this->from);

        // save tags
        if ($tags != '') {
            $saveWorkingLanguage = BL::getWorkingLanguage();

            // If we don't set the working language to the target language,
            // BackendTagsModel::getURL() will use the current working
            // language, possibly causing unnecessary '-2' suffixes in
            // tags.url
            BL::setWorkingLanguage($this->to);

            BackendTagsModel::saveTags($page['id'], $tags, 'pages', $this->to);
            BL::setWorkingLanguage($saveWorkingLanguage);
        }
    }
}
