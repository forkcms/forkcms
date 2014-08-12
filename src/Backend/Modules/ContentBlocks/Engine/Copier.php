<?php

namespace Backend\Modules\ContentBlocks\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;

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

    public function __construct(\SpoonDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * Copy content blocks
     *
     * @param string $from     The language code to copy the pages from.
     * @param string $to       The language code we want to copy the pages to.
     * @param int    $fromSite The site_id code to copy the pages from.
     * @param int    $toSite   The site_id code we want to copy the pages to.
     * @return array
     */
    public function copy($from, $to, $fromSite, $toSite)
    {
        $copyDate = BackendModel::getUTCDate();

        // init variables
        $contentBlockIds = $oldIds = $newIds = array();

        // copy the contentblocks
        $contentBlocks = (array) $this->database->getRecords(
            'SELECT *
             FROM content_blocks
             WHERE language = ? AND site_id = ? AND status = ?',
            array($from, $fromSite, 'active')
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
            $newBlock['id'] = Model::getMaximumId() + $i;
            $newBlock['language'] = $to;
            $newBlock['site_id'] = $toSite;
            $newBlock['created_on'] = $copyDate;
            $newBlock['edited_on'] = $copyDate;
            $newBlock['status'] = $contentBlock['status'];
            $newBlock['user_id'] = BackendAuthentication::getUser()->getUserId();
            $newBlock['template'] = $contentBlock['template'];
            $newBlock['title'] = $contentBlock['title'];
            $newBlock['text'] = $contentBlock['text'];
            $newBlock['hidden'] = $contentBlock['hidden'];

            // insert content block
            $newId = Model::insert($newBlock);

            // save ids for later
            $oldIds[] = $oldId;
            $newIds[$oldId] = $newId;

            // redefine counter
            $i++;
        }

        // get the extra Ids for the content blocks
        if (!empty($newIds)) {
            // get content block extra ids
            $contentBlockExtraIds = (array) $this->database->getRecords(
                'SELECT revision_id, extra_id
                 FROM content_blocks
                 WHERE revision_id IN (' . implode(',', $newIds) . ')'
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
}
