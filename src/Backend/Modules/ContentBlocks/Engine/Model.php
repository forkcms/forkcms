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
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class Model
{
    const ENTITY_CLASS = 'Backend\Modules\ContentBlocks\Entity\ContentBlock';

    /**
     * Copy content blocks
     *
     * @param string $from The language code to copy the content blocks from.
     * @param string $to   The language code we want to copy the content blocks to.
     * @return array
     */
    public static function copy($from, $to)
    {
        // get entity manager
        $em = BackendModel::get('doctrine.orm.entity_manager');

        // init variables
        $contentBlockIds = array();

        // copy the contentblocks
        $contentBlocks = $em
            ->getRepository(self::ENTITY_CLASS)
            ->findBy(
                array(
                    'status'   => ContentBlock::STATUS_ACTIVE,
                    'language' => $from,
                )
            )
        ;

        // define counter
        $i = 1;

        // loop existing content blocks
        foreach ($contentBlocks as $contentBlock) {
            // build new block
            $newContentBlock = new ContentBlock();
            $newContentBlock
                ->setId(self::getMaximumId() + $i)
                ->setLanguage($to)
                ->setStatus($contentBlock->getStatus())
                ->setUserId(BackendAuthentication::getUser()->getUserId())
                ->setTemplate($contentBlock->getTemplate())
                ->setTitle($contentBlock->getTitle())
                ->setText($contentBlock->getText())
                ->setIsHidden($contentBlock->getIsHidden())
            ;

            // inset content block
            self::insert($newContentBlock);

            $contentBlockIds[$contentBlock->getExtraId()] = $newContentBlock->getExtraId();

            // redefine counter
            $i++;
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
        $contentBlocksToDelete = $em
            ->getRepository(self::ENTITY_CLASS)
            ->findBy(
                array(
                    'id' => $contentBlock->getId(),
                    'language' => $contentBlock->getLanguage(),
                )
            )
        ;
        foreach ($contentBlocksToDelete as $contentBlockToDelete) {
            $em->remove($contentBlockToDelete);
        }
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
            ->getRepository(self::ENTITY_CLASS)
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
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $maxContentBlock = $em
            ->getRepository(self::ENTITY_CLASS)
            ->findOneBy(
                array('language' => BL::getWorkingLanguage()),
                array('id' => 'DESC')
            )
        ;

        return empty($maxContentBlock) ? 0 : $maxContentBlock->getId();
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
            ->getRepository(self::ENTITY_CLASS)
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
            ->getRepository(self::ENTITY_CLASS)
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
            ->getRepository(self::ENTITY_CLASS)
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
