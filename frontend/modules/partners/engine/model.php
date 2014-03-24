<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the frontend model for the partners module.
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class FrontendPartnersModel
{
    /**
     * The location where the images are stored within the files directory
     */
    const IMAGE_PATH = 'partners';

    /**
     * The location where the thumbnails are stored within the files directory
     */
    const THUMBNAIL_PATH = 'partners/48x48';

    /**
     * Get all items of a specific slider
     *
     * @param   int       $id       slider id
     * @return  array
     */
    public static function getSlidersPartners($id)
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.name, i.img, i.url, i.widget
             FROM partners AS i
             WHERE widget = ?
             ORDER BY sequence',
            $id
        );

        return $items;
    }
}
