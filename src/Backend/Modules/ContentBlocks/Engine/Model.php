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
         WHERE i.status = ? AND i.language = ? AND i.site_id = ?';

    const QRY_BROWSE_REVISIONS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM content_blocks AS i
         WHERE i.status = ? AND i.id = ? AND i.language = ? AND i.site_id = ?
         ORDER BY i.edited_on DESC';

    /**
     * Delete an item.
     *
     * @param int $id The id of the record to delete.
     */
    public static function delete($id)
    {
        $id = (int) $id;
        $db = BackendModel::getContainer()->get('database');

        // get item
        $item = self::get($id);

        // build extra
        $extra = array(
            'id' => $item['extra_id'],
            'module' => 'ContentBlocks',
            'type' => 'widget',
            'action' => 'Detail'
        );

        // delete extra
        $db->delete(
            'modules_extras',
            'id = ? AND module = ? AND type = ? AND action = ?',
            array($extra['id'], $extra['module'], $extra['type'], $extra['action'])
        );

        // update blocks with this item linked
        $db->update(
            'pages_blocks',
            array('extra_id' => null, 'html' => ''),
            'extra_id = ?',
            array($item['extra_id'])
        );

        // delete all records
        $db->delete(
            'content_blocks',
            'id = ? AND language = ? AND site_id = ?',
            array(
                $id,
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
            )
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
                 WHERE i.id = ? AND i.status = ? AND i.language = ? AND i.site_id = ?
                 LIMIT 1',
                array(
                    (int) $id,
                    'active',
                    BL::getWorkingLanguage(),
                    BackendModel::get('current_site')->getId(),
                )
            );
        }

        // fallback, this doesn't take the active status in account
        return (bool) $db->getVar(
            'SELECT 1
             FROM content_blocks AS i
             WHERE i.revision_id = ? AND i.language = ? AND i.site_id = ?
             LIMIT 1',
            array(
                (int) $id,
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
            )
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
            'SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on,
             UNIX_TIMESTAMP(i.edited_on) AS edited_on
             FROM content_blocks AS i
             WHERE i.id = ? AND i.status = ? AND i.language = ? AND i.site_id = ?
             LIMIT 1',
            array(
                (int) $id,
                'active',
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
            )
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
            'SELECT MAX(i.id)
             FROM content_blocks AS i
             WHERE i.language = ? AND i.site_id = ?
             LIMIT 1',
            array(
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
            )
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
            'SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on,
             UNIX_TIMESTAMP(i.edited_on) AS edited_on
             FROM content_blocks AS i
             WHERE i.id = ? AND i.revision_id = ? AND i.language = ? AND i.site_id = ?
             LIMIT 1',
            array(
                (int) $id,
                (int) $revisionId,
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
            )
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
        $db = BackendModel::getContainer()->get('database');

        // build extra
        $extra = array(
            'module' => 'ContentBlocks',
            'type' => 'widget',
            'label' => 'ContentBlocks',
            'action' => 'Detail',
            'data' => null,
            'hidden' => 'N',
            'sequence' => $db->getVar(
                'SELECT MAX(i.sequence) + 1
                 FROM modules_extras AS i
                 WHERE i.module = ?',
                array('ContentBlocks')
            )
        );

        if (is_null($extra['sequence'])) {
            $extra['sequence'] = $db->getVar(
                'SELECT CEILING(MAX(i.sequence) / 1000) * 1000
                 FROM modules_extras AS i'
            );
        }

        // insert extra
        $item['extra_id'] = $db->insert('modules_extras', $extra);
        $extra['id'] = $item['extra_id'];

        // insert and return the new revision id
        $item['revision_id'] = $db->insert('content_blocks', $item);

        // update extra (item id is now known)
        $extra['data'] = serialize(
            array(
                'id' => $item['id'],
                'extra_label' => $item['title'],
                'language' => $item['language'],
                'site_id' => $item['site_id'],
                'edit_url' => BackendModel::createURLForAction(
                    'Edit',
                    'ContentBlocks',
                    $item['language']
                ) . '&id=' . $item['id']
            )
        );
        $db->update(
            'modules_extras',
            $extra,
            'id = ? AND module = ? AND type = ? AND action = ?',
            array($extra['id'], $extra['module'], $extra['type'], $extra['action'])
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

        // build extra
        $extra = array(
            'id' => $item['extra_id'],
            'module' => 'ContentBlocks',
            'type' => 'widget',
            'label' => 'ContentBlocks',
            'action' => 'Detail',
            'data' => serialize(
                array(
                    'id' => $item['id'],
                    'extra_label' => $item['title'],
                    'language' => $item['language'],
                    'site_id' => $item['site_id'],
                    'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $item['id']
                )
            ),
            'hidden' => 'N'
        );

        // update extra
        $db->update(
            'modules_extras',
            $extra,
            'id = ? AND module = ? AND type = ? AND action = ?',
            array($extra['id'], $extra['module'], $extra['type'], $extra['action'])
        );

        // archive all older versions
        $db->update(
            'content_blocks',
            array('status' => 'archived'),
            'id = ? AND language = ? AND site_id = ?',
            array($item['id'], $item['language'], $item['site_id'])
        );

        // insert new version
        $item['revision_id'] = $db->insert('content_blocks', $item);

        // how many revisions should we keep
        $rowsToKeep = (int) BackendModel::getModuleSetting('ContentBlocks', 'max_num_revisions', 20);

        // get revision-ids for items to keep
        $revisionIdsToKeep = (array) $db->getColumn(
            'SELECT i.revision_id
             FROM content_blocks AS i
             WHERE i.id = ? AND i.language = ? AND i.site_id = ? AND i.status = ?
             ORDER BY i.edited_on DESC
             LIMIT ?',
            array($item['id'], $item['language'], $item['site_id'], 'archived', $rowsToKeep)
        );

        // delete other revisions
        if (!empty($revisionIdsToKeep)) {
            $db->delete(
                'content_blocks',
                'id = ? AND language = ? AND site_id = ? AND status = ?
                 AND revision_id NOT IN (' . implode(', ', $revisionIdsToKeep) . ')',
                array($item['id'], $item['language'], $item['site_id'], 'archived')
            );
        }

        // return the new revision_id
        return $item['revision_id'];
    }
}
