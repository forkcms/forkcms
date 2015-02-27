<?php

namespace Backend\Modules\ContentBlocks\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Finder\Finder;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the content_blocks module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class Model
{
    const QRY_BROWSE =
        'SELECT i.id, i.title, i.hidden
         FROM content_blocks AS i
         WHERE i.status = ? AND i.language = ?';

    const QRY_BROWSE_REVISIONS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM content_blocks AS i
         WHERE i.status = ? AND i.id = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    /**
     * Copy content blocks
     *
     * @param string $from The language code to copy the content blocks from.
     * @param string $to   The language code we want to copy the content blocks to.
     * @return array
     */
    public static function copy($from, $to)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // init variables
        $contentBlockIds = $oldIds = $newIds = array();

        // copy the contentblocks
        $contentBlocks = (array) $db->getRecords(
            'SELECT * FROM content_blocks WHERE language = ? AND status = "active"',
            array($from)
        );

        // define counter
        $i = 1;

        // loop existing content blocks
        foreach ($contentBlocks as $contentBlock) {
            // define old id
            $oldId = $contentBlock['extra_id'];

            // init new block
            $newBlock = array();

            // build new block
            $newBlock['id'] = static::getMaximumId() + $i;
            $newBlock['language'] = $to;
            $newBlock['created_on'] = BackendModel::getUTCDate();
            $newBlock['edited_on'] = BackendModel::getUTCDate();
            $newBlock['status'] = $contentBlock['status'];
            $newBlock['user_id'] = BackendAuthentication::getUser()->getUserId();
            $newBlock['template'] = $contentBlock['template'];
            $newBlock['title'] = $contentBlock['title'];
            $newBlock['text'] = $contentBlock['text'];
            $newBlock['hidden'] = $contentBlock['hidden'];

            // inset content block
            $newId = static::insert($newBlock);

            // save ids for later
            $oldIds[] = $oldId;
            $newIds[$oldId] = $newId;

            // redefine counter
            $i++;
        }

        // get the extra Ids for the content blocks
        if (!empty($newIds)) {
            // get content block extra ids
            $contentBlockExtraIds = (array) $db->getRecords(
                'SELECT revision_id, extra_id FROM content_blocks WHERE revision_id IN (' . implode(',', $newIds) . ')'
            );

            // loop new ids
            foreach ($newIds as $oldId => $newId) {
                foreach ($contentBlockExtraIds as $extraId) {
                    if ($extraId['revision_id'] == $newId) {
                        $contentBlockIds[$oldId] = $extraId['extra_id'];
                    }
                }
            }
        }

        // return contentBlockIds
        return $contentBlockIds;
    }

    /**
     * Delete an item.
     *
     * @param int $id The id of the record to delete.
     */
    public static function delete($id)
    {
        // recast id
        $id = (int) $id;

        // get item
        $item = static::get($id);

        // delete extra and pages_blocks
        BackendModel::deleteExtraById($item['extra_id']);

        // delete the content_block
        BackendModel::getContainer()->get('database')->delete(
            'content_blocks',
            'id = ? AND language = ?',
            array($id, BL::getWorkingLanguage())
        );
    }

    /**
     * Does the item exist.
     *
     * @param int  $id         The id of the record to check for existence.
     * @param bool $activeOnly Only check in active items?
     * @return bool
     */
    public static function exists($id, $activeOnly = true)
    {
        $db = BackendModel::getContainer()->get('database');

        // if the item should also be active, there should be at least one row to return true
        if ((bool) $activeOnly) {
            return (bool) $db->getVar(
                'SELECT 1
                 FROM content_blocks AS i
                 WHERE i.id = ? AND i.status = ? AND i.language = ?
                 LIMIT 1',
                array((int) $id, 'active', BL::getWorkingLanguage())
            );
        }

        // fallback, this doesn't take the active status in account
        return (bool) $db->getVar(
            'SELECT 1
             FROM content_blocks AS i
             WHERE i.revision_id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Get all data for a given id.
     *
     * @param int $id The id for the record to get.
     * @return array
     */
    public static function get($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on
             FROM content_blocks AS i
             WHERE i.id = ? AND i.status = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, 'active', BL::getWorkingLanguage())
        );
    }

    /**
     * Get the maximum id.
     *
     * @return int
     */
    public static function getMaximumId()
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.id) FROM content_blocks AS i WHERE i.language = ? LIMIT 1',
            array(BL::getWorkingLanguage())
        );
    }

    /**
     * Get all data for a given revision.
     *
     * @param int $id         The Id for the item wherefore you want a revision.
     * @param int $revisionId The Id of the revision.
     * @return array
     */
    public static function getRevision($id, $revisionId)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on
             FROM content_blocks AS i
             WHERE i.id = ? AND i.revision_id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, (int) $revisionId, BL::getWorkingLanguage())
        );
    }

    /**
     * Get templates.
     *
     * @return array
     */
    public static function getTemplates()
    {
        $templates = array();
        $finder = new Finder();
        $finder->name('*.tpl');
        $finder->in(FRONTEND_MODULES_PATH . '/ContentBlocks/Layout/Widgets');

        // if there is a custom theme we should include the templates there also
        $theme = BackendModel::getModuleSetting('Core', 'theme', 'core');
        if ($theme != 'core') {
            $path = FRONTEND_PATH . '/Themes/' . $theme . '/Modules/ContentBlocks/Layout/Widgets';
            if (is_dir($path)) {
                $finder->in($path);
            }
        }

        foreach ($finder->files() as $file) {
            $templates[] = $file->getBasename();
        }

        return array_unique($templates);
    }

    /**
     * Add a new item.
     *
     * @param array $item The data to insert.
     * @return int
     */
    public static function insert(array $item)
    {
        // insert extra
        $item['extra_id'] = BackendModel::insertExtra(
            'widget',
            'ContentBlocks',
            'Detail'
        );

        $item['revision_id'] = BackendModel::get('database')
            ->insert('content_blocks', $item)
        ;

        // update data for the extra
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            array(
                'id' => $item['id'],
                'extra_label' => $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createURLForAction(
                    'Edit',
                    'ContentBlocks',
                    $item['language']
                ) . '&id=' . $item['id']
            )
        );

        return $item['revision_id'];
    }

    /**
     * Update an existing item.
     *
     * @param array $item The new data.
     * @return int
     */
    public static function update(array $item)
    {
        $db = BackendModel::getContainer()->get('database');

        // update extra
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            array(
                'id' => $item['id'],
                'extra_label' => $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $item['id']
            )
        );

        // archive all older content_block versions
        $db->update(
            'content_blocks',
            array('status' => 'archived'),
            'id = ? AND language = ?',
            array($item['id'], BL::getWorkingLanguage())
        );

        // insert new version
        $item['revision_id'] = $db->insert('content_blocks', $item);

        // how many revisions should we keep
        $rowsToKeep = (int) BackendModel::getModuleSetting('ContentBlocks', 'max_num_revisions', 20);

        // get revision-ids for items to keep
        $revisionIdsToKeep = (array) $db->getColumn(
            'SELECT i.revision_id
             FROM content_blocks AS i
             WHERE i.id = ? AND i.language = ? AND i.status = ?
             ORDER BY i.edited_on DESC
             LIMIT ?',
            array($item['id'], BL::getWorkingLanguage(), 'archived', $rowsToKeep)
        );

        // delete other revisions
        if (!empty($revisionIdsToKeep)) {
            $db->delete(
                'content_blocks',
                'id = ? AND language = ? AND status = ? AND revision_id NOT IN (' . implode(', ', $revisionIdsToKeep) . ')',
                array($item['id'], BL::getWorkingLanguage(), 'archived')
            );
        }

        // return the new revision_id
        return $item['revision_id'];
    }
}
