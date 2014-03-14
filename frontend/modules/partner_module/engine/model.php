<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the frontend model for the partner module.
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class FrontendPartnerModuleModel
{
    /**
     * The location where the images are stored within the files directory
     */
    const IMAGE_PATH = 'partner_module';

    /**
     * The location where the thumbnails are stored within the files directory
     */
    const THUMBNAIL_PATH = 'partner_module/180x180';

    /**
     * Get all items
     *
     * @return array
     */
    public static function getAll()
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.name, i.img, i.url
             FROM partner_module AS i'
        );

        return $items;
    }
}
