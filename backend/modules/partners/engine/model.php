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
        'SELECT i.id, i.name
         FROM partners_widgets AS i';

    /**
     * The browse partners of a widget query for the datagrid
     */
    const QRY_DATAGRID_BROWSE_PARTNERS =
        'SELECT i.id, i.name, i.img, i.url, i.sequence, i.widget
         FROM partners AS i
         WHERE widget = ?
         ORDER BY sequence';

    /**
     * Deletes a partner
     *
     * @param int $id
     */
    public static function deletePartner($id)
    {
        BackendModel::getContainer()->get('database')->delete('partners', 'id = ?', array((int)$id));
    }

    /**
     * Deletes the partners of a widget
     *
     * @param int $widget
     */
    public static function deleteWidgetPartners($widget)
    {
        BackendModel::getContainer()->get('database')->delete('partners', 'widget = ?', array((int)$widget));
    }

    /**
     * Deletes a widgets
     *
     * @param int $id
     * @param int $widgetId
     */
    public static function deleteWidget($id, $widgetId)
    {
        $id = (int) $id;
        $db = BackendModel::getContainer()->get('database');

        // delete records
        $db->delete('partners_widgets', 'id = ?', $id);
        $db->delete(
            'modules_extras',
            'id = ? AND module = ? AND type = ? AND action = ?',
            array((int)$widgetId, 'partners', 'widget', 'Slideshow')
        );

        self::deleteWidgetPartners($id);
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
            'SELECT i.id, i.name, i.img, i.widget, i.url, i.created_by, i.created_on, i.edited_on
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
            'SELECT i.id, i.name, i.widget_id, i.created_by, i.created_on, i.edited_on
             FROM partners_widgets AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Get the maximum partner id
     *
     * @param $widgetId
     * @return int
     */
    public static function getMaximumPartnerId($widgetId)
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(id) FROM partners WHERE widget = ?',
            array(
                (int) $widgetId
            )
        );
    }

    /**
     * Get the maximum widget id
     *
     * @return int
     */
    public static function getMaximumWidgetId()
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(id) FROM partners_widgets'
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
             WHERE id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Checks if a widget exists
     *
     * @param int $id
     * @return bool
     */
    public static function widgetExists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM partners_widgets
             WHERE id = ?
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
        $db = BackendModel::getContainer()->get('database');

        //set extra details
        $item['created_by'] = BackendAuthentication::getUser()->getUserId();
        $item['created_on'] = date('Y-m-d H:i:s');
        $item['edited_on'] = date('Y-m-d H:i:s');
        $item['sequence'] = (int) $db->getVar(
            'SELECT MAX(sequence) FROM partners'
        ) + 1;
        // insert and return the new partner id
        $item['id'] = $db->insert(
            'partners',
            $item
        );

        return $item['id'];
    }

    /**
     * Inserts a widget into the database
     *
     * @param array $item
     * @return int
     */
    public static function insertWidget(array $item)
    {
        $db = BackendModel::getContainer()->get('database');

        //set extra details
        $item['created_by'] = BackendAuthentication::getUser()->getUserId();
        $item['created_on'] = date('Y-m-d H:i:s');
        $item['edited_on'] = date('Y-m-d H:i:s');

        // insert and return the new partner id
        $item['id'] = $db->insert(
            'partners_widgets',
            $item
        );

        // set next sequence number for this module
        $sequence = (int) $db->getVar(
            'SELECT MAX(sequence) + 1 FROM modules_extras WHERE module = ?',
            array((string) 'partners')
        );

        // this is the first extra for this module: generate new 1000-series
        if (is_null($sequence)) {
            $sequence = (int) $db->getVar(
                'SELECT CEILING(MAX(sequence) / 1000) * 1000 FROM modules_extras'
            );
        }

        $data = array();
        $data['partners_widget_id'] = $item['id'];
        $data['extra_label'] = $item['name'];

        // build widget
        $widget = array(
            'module' => 'partners',
            'type' => 'widget',
            'label' => 'slideshow',
            'action' => 'Slideshow',
            'data' => serialize($data),
            'sequence' => $sequence
        );

        // build query
        $query = 'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND label = ?  AND data = ?';
        $parameters = array($widget['module'], $widget['type'], $widget['label'], $data);

        // get id (if its already exists)
        $widgetId = (int) $db->getVar($query, $parameters);

        // doesn't already exist
        if ($widgetId === 0) {
            $widgetId = $db->insert('modules_extras', $widget);
        }
        // add widget id to widget so we can update the widget
        $db->update('partners_widgets', array('widget_id' => $widgetId), 'id = ?', $item['id']);

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

        return $item['id'];
    }

    /**
     * Update a widget
     *
     * @param array $item
     * @return int
     */
    public static function updateWidget(array $item)
    {
        $db = BackendModel::getContainer()->get('database');

        //set update time
        $item['edited_on'] = date('Y-m-d H:i:s');

        // update
        $db->update(
            'partners_widgets',
            $item,
            'id = ?',
            array($item['id'])
        );

        $data = array();
        $data['partners_widget_id'] = $item['id'];
        $data['extra_label'] = $item['name'];

        // build widget
        $widget = array(
            'module' => 'partners',
            'type' => 'widget',
            'label' => 'slideshow',
            'action' => 'Slideshow',
            'data' => serialize($data)
        );

        // update extra
        $db->update(
            'modules_extras',
            $widget,
            'id = ? AND module = ? AND type = ? AND action = ?',
            array($item['widget_id'], $widget['module'], $widget['type'], $widget['action'])
        );

        return $item['id'];
    }
}
