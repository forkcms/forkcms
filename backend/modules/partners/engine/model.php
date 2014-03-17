<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the model for the partners module.
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class BackendPartnersModel
{
    /**
     * The browse widgets query for the datagrid
     */
    const QRY_DATAGRID_BROWSE_SLIDERS =
        'SELECT i.id, i.name, i.created_by, i.created_on, i.edited_on
         FROM partners_widgets AS i';

    /**
     * The browse partners of a slider query for the datagrid
     */
    const QRY_DATAGRID_BROWSE_PARTNERS =
        'SELECT i.id, i.name, i.img, i.url, i.created_by, i.created_on, i.edited_on
         FROM partners AS i
         WHERE slider = ?';

    /**
     * Deletes one or more partners
     *
     * @param int $id
     */
    public static function deletePartner($id)
    {
        $id = (int) $id;
        $db = BackendModel::getContainer()->get('database');

        // delete records
        $db->delete('partners', 'id = ? && slider = ?', array($id));

        // invalidate the cache for partner_module
        BackendModel::invalidateFrontendCache('partners');
    }

    /**
     * Deletes one or more widgets
     *
     * @param int $id
     */
    public static function deleteWidget($id)
    {
        $id = (int) $id;
        $db = BackendModel::getContainer()->get('database');

        // delete records
        $db->delete('partner_widgets', 'id = ?', $id);

        // invalidate the cache for partners module
        BackendModel::invalidateFrontendCache('partners');
    }

    /**
     * Get all data for a given id
     *
     * @param int $id
     * @return array
     */
    public static function getPartner($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.id, i.name, i.img, i.url, i.created_by, i.created_on, i.edited_on
             FROM partners AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Get all data for a given id
     *
     * @param int $id
     * @return array
     */
    public static function getWidget($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.id, i.name, i.img, i.url, i.created_by, i.created_on, i.edited_on
             FROM partners_widgets AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Get the maximum partner id
     *
     * @return int
     */
    public static function getMaximumPartnerId()
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(id) FROM partners LIMIT 1'
        );
    }

    /**
     * Get the maximum slide id
     *
     * @return int
     */
    public static function getMaximumWidgetId()
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(id) FROM partners_widgets LIMIT 1'
        );
    }

    /**
     * Checks if a partner exists
     *
     * @param int $id
     * @return bool
     */
    public static function partnerExists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM partners
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Checks if a slider exists
     *
     * @param int $id
     * @return bool
     */
    public static function sliderExists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM partners_widgets
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Inserts a partner into the database
     *
     * @param array $item
     * @return int
     */
    public static function insertPartner(array $item)
    {
        //set extra details
        $item['created_by'] = BackendAuthentication::getUser()->getUserId();
        $item['created_on'] = date('Y-m-d H:i:s');
        $item['edited_on'] = date('Y-m-d H:i:s');

        // insert and return the new partner id
        $item['id'] = BackendModel::getContainer()->get('database')->insert(
            'partners',
            $item
        );

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('partners');

        return $item['id'];
    }

    /**
     * Inserts a slider into the database
     *
     * @param array $item
     * @return int
     */
    public static function insertWidget(array $item)
    {
        //set extra details
        $item['created_by'] = BackendAuthentication::getUser()->getUserId();
        $item['created_on'] = date('Y-m-d H:i:s');
        $item['edited_on'] = date('Y-m-d H:i:s');

        // insert and return the new partner id
        $item['id'] = BackendModel::getContainer()->get('database')->insert(
            'partners_widgets',
            $item
        );

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('partners');

        return $item['id'];
    }

    /**
     * Update a partner
     *
     * @param array $item
     * @return int
     */
    public static function updatePartner(array $item)
    {
        //set update time
        $item['edited_on'] = date('Y-m-d H:i:s');

        // update
        BackendModel::getContainer()->get('database')->update(
            'partners',
            $item,
            'id = ?',
            array($item['id'])
        );

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('partner_module');

        return $item['id'];
    }
    /**
     * Update a slider
     *
     * @param array $item
     * @return int
     */
    public static function updateWidget(array $item)
    {
        //set update time
        $item['edited_on'] = date('Y-m-d H:i:s');

        // update
        BackendModel::getContainer()->get('database')->update(
            'partners_widgets',
            $item,
            'id = ?',
            array($item['id'])
        );

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('partner_module');

        return $item['id'];
    }
}
