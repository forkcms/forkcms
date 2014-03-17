<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the model voor the partners module.
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class BackendPartnersModel
{
    /**
     * The browse query for the datagrid
     */
    const QRY_DATAGRID_BROWSE =
        'SELECT i.id, i.name, i.img, i.url, i.created_by, i.created_on, i.edited_on
         FROM partner_module AS i';

    /**
     * Deletes one or more items
     *
     * @param int $id
     */
    public static function delete($id)
    {
        $id = (int) $id;
        $db = BackendModel::getContainer()->get('database');

        // delete records
        $db->delete('partner_module', 'id = ?', $id);

        // invalidate the cache for partner_module
        BackendModel::invalidateFrontendCache('partner_module');
    }

    /**
     * Get all data for a given id
     *
     * @param int $id
     * @return array
     */
    public static function get($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.id, i.name, i.img, i.url, i.created_by, i.created_on, i.edited_on
             FROM partner_module AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Get the maximum id
     *
     * @return int
     */
    public static function getMaximumId()
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(id) FROM partner_module LIMIT 1'
        );
    }

    /**
     * Checks if an item exists
     *
     * @param int $id
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM partner_module
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Inserts an item into the database
     *
     * @param array $item
     * @return int
     */
    public static function insert(array $item)
    {
        //set extra details
        $item['created_by'] = BackendAuthentication::getUser()->getUserId();
        $item['created_on'] = date('Y-m-d H:i:s');
        $item['edited_on'] = date('Y-m-d H:i:s');

        // insert and return the new partner id
        $item['id'] = BackendModel::getContainer()->get('database')->insert(
            'partner_module',
            $item
        );

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('partner_module');

        return $item['id'];
    }

    /**
     * Update an existing item
     *
     * @param array $item
     * @return int
     */
    public static function update(array $item)
    {
        //set update time
        $item['edited_on'] = date('Y-m-d H:i:s');

        // update
        BackendModel::getContainer()->get('database')->update(
            'partner_module',
            $item,
            'id = ?',
            array($item['id'])
        );

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('partner_module');

        return $item['id'];
    }
}
