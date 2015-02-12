<?php

namespace Backend\Modules\Location\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Entity\Location;

/**
 * In this file we store all generic functions that we will be using in the location module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class Model
{
    const LOCATION_ENTITY_CLASS = 'Backend\Modules\Location\Entity\Location';

    const QRY_DATAGRID_BROWSE =
        'SELECT id, title, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
         FROM location
         WHERE language = ?';

    /**
     * Delete an item
     *
     * @param Location $location
     */
    public static function delete(Location $location)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->remove($location);
        $em->flush();
    }

    /**
     * Check if an item exists
     *
     * @param int $id The id of the record to look for.
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM location AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Fetch a record from the database
     *
     * @param int $id The id of the record to fetch.
     * @return Location
     */
    public static function get($id)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em->getRepository('Backend\Modules\Location\Entity\Location')->findOneBy(
            array(
                'id' => $id,
                'language' => BL::getWorkingLanguage()
            )
        );
    }

    /**
     * Fetch a record from the database
     *
     * @return array
     */
    public static function getAll()
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em->getRepository('Backend\Modules\Location\Entity\Location')->findBy(
            array(
                'language' => BL::getWorkingLanguage(),
                'showOverview' => true
            )
        );
    }

    /**
     * Insert an item
     *
     * @param Location $location
     * @return int
     */
    public static function insert(Location $location)
    {
        // insert extra
        $location->setExtraId(BackendModel::insertExtra(
            'widget',
            'Location'
        ));

        // insert new location
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($location);
        $em->flush();

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $location->getExtraId(),
            'data',
            array(
                'id' => $location->getId(),
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'Core')) . ': ' . $location->getTitle(),
                'language' => $location->getLanguage(),
                'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $location->getId()
            )
        );

        return $location->getId();
    }

    /**
     * Update an item
     *
     * @param Location $location
     * @return int
     */
    public static function update(Location $location)
    {
        // we have an extra_id
        $extraId = $location->getExtraId();
        if (isset($extraId)) {
            // update extra (item id is now known)
            BackendModel::updateExtra(
                $location->getExtraId(),
                'data',
                array(
                    'id' => $location->getId(),
                    'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'Core')) . ': ' . $location->getTitle(),
                    'language' => $location->getLanguage(),
                    'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $location->getId()
                )
            );
        }

        // update location
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($location);
        $em->flush();

        return $location->getId();
    }
}
