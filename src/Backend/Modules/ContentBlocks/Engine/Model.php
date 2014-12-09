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
use Backend\Modules\ContentBlocks\Entity\ContentBlock;

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
            $newBlock['id'] = self::getMaximumId() + $i;
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
            $newId = self::insert($newBlock);

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
     * @param ContentBlock $contentBlock The record to delete.
     */
    public static function delete(ContentBlock $contentBlock)
    {
        // delete extra and pages_blocks
        BackendModel::deleteExtraById($contentBlock->getExtraId());

        // delete the content_block
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->remove($contentBlock);
        $em->flush();
    }

    /**
     * Get all data for a given id.
     *
     * @param int $id The id for the record to get.
     * @return array
     */
    public static function get($id)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em
            ->getRepository('Backend\Modules\ContentBlocks\Entity\ContentBlock')
            ->findOneBy(
                array(
                    'id'       => $id,
                    'status'   => ContentBlock::STATUS_ACTIVE,
                    'language' => BL::getWorkingLanguage(),
                )
            )
        ;
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
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em
            ->getRepository('Backend\Modules\ContentBlocks\Entity\ContentBlock')
            ->findOneBy(
                array(
                    'id'         => $id,
                    'revisionId' => $revisionId,
                    'language'   => BL::getWorkingLanguage(),
                )
            )
        ;
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
     * @param  ContentBlock $item The data to insert.
     * @return int
     */
    public static function insert(ContentBlock $contentBlock)
    {
        // insert extra
        $contentBlock->setExtraId(BackendModel::insertExtra(
            'widget',
            'ContentBlocks',
            'Detail'
        ));

        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($contentBlock);
        $em->flush();

        // update data for the extra
        BackendModel::updateExtra(
            $contentBlock->getExtraId(),
            'data',
            array(
                'id' => $contentBlock->getId(),
                'extra_label' => $contentBlock->getTitle(),
                'language' => $contentBlock->getLanguage(),
                'edit_url' => BackendModel::createURLForAction(
                    'Edit',
                    'ContentBlocks',
                    $contentBlock->getLanguage()
                ) . '&id=' . $contentBlock->getId()
            )
        );

        return $contentBlock->getRevisionId();
    }

    /**
     * Update an existing item.
     *
     * @param  ContentBlock $item The new data.
     * @return int
     */
    public static function update(ContentBlock $contentBlock)
    {
        $db = BackendModel::getContainer()->get('database');
        $em = BackendModel::get('doctrine.orm.entity_manager');

        // update extra
        BackendModel::updateExtra(
            $contentBlock->getExtraId(),
            'data',
            array(
                'id' => $contentBlock->getId(),
                'extra_label' => $contentBlock->getTitle(),
                'language' => $contentBlock->getLanguage(),
                'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $contentBlock->getId()
            )
        );

        // archive all older content_block versions
        $itemsToArchive = $em
            ->getRepository('Backend\Modules\ContentBlocks\Entity\ContentBlock')
            ->findBy(
                array(
                    'id' => $contentBlock->getId(),
                    'language' => $contentBlock->getLanguage(),
                )
            )
        ;
        foreach ($itemsToArchive as $itemToArchive) {
            $itemToArchive->setStatus(ContentBlock::STATUS_ARCHIVED);
            $em->persist($itemToArchive);
        }

        // insert new version
        $em->persist($contentBlock);

        // how many revisions should we keep
        $rowsToKeep = (int) BackendModel::getModuleSetting('ContentBlocks', 'max_num_revisions', 20);

        // get revision-ids for items to keep
        $revisionsToRemove = $em
            ->getRepository('Backend\Modules\ContentBlocks\Entity\ContentBlock')
            ->findBy(
                array(
                    'id' => $contentBlock->getId(),
                    'language' => $contentBlock->getLanguage(),
                    'status' => ContentBlock::STATUS_ARCHIVED,
                ),
                array(
                    'editedOn' => 'DESC',
                ),
                null,
                $rowsToKeep - 1
            )
        ;

        // delete other revisions
        if (!empty($revisionsToRemove)) {
            foreach ($revisionsToRemove as $revisionToRemove) {
                $em->remove($revisionToRemove);
            }
        }
        $em->flush();

        // return the new revision_id
        return $contentBlock->getRevisionId();
    }
}
