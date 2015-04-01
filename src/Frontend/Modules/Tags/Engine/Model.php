<?php

namespace Frontend\Modules\Tags\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * In this file we store all generic functions that we will be using in the tags module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class Model
{
    /**
     * Calls a method that has to be implemented though the tags interface
     *
     * @param string       $module    The module wherein to search.
     * @param string       $class     The class that should contain the method.
     * @param string       $method    The method to call.
     * @param mixed $parameter The parameters to pass.
     * @return mixed
     * @throws FrontendException When FrontendTagsInterface is not correctly implemented to the module model
     */
    public static function callFromInterface($module, $class, $method, $parameter = null)
    {
        // check to see if the interface is implemented
        if (in_array('Frontend\\Modules\\Tags\\Engine\\TagsInterface', class_implements($class))) {
            // return result
            return call_user_func(array($class, $method), $parameter);
        } else {
            throw new FrontendException(
                'To use the tags module you need
                to implement the FrontendTagsInterface
                in the model of your module
                (' . $module . ').'
            );
        }
    }

    /**
     * Get the tag for a given URL
     *
     * @param string        $URL The URL to get the tag for.
     * @param string $language The language for the item.
     * @return Tag          A tag
     */
    public static function get($URL, $language = null)
    {
        $language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

        return FrontendModel::get('doctrine.orm.entity_manager')
            ->getRepository(BackendTagsModel::ENTITY_CLASS)
            ->findOneBy(
                array(
                    'url'      => (string) $URL,
                    'language' => $language,
                )
            )
        ;
    }

    /**
     * Fetch the list of all tags, ordered by their occurrence
     *
     * @param string $language The language for the items.
     * @return Tag[] All tags
     */
    public static function getAll($language = null)
    {
        $language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

        return FrontendModel::get('doctrine.orm.entity_manager')
            ->getRepository(BackendTagsModel::ENTITY_CLASS)
            ->createQueryBuilder('i')
            ->orderBy('i.tag', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get tags for an item
     *
     * @param string $module  The module wherein the otherId occurs.
     * @param int    $otherId The id of the item.
     * @param string $language The language for the items.
     * @return Tag[] All tags for item
     */
    public static function getForItem($module, $otherId, $language = null)
    {
        $language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

        return FrontendModel::get('doctrine.orm.entity_manager')
            ->getRepository(BackendTagsModel::ENTITY_CLASS)
            ->createQueryBuilder('i')
            ->leftJoin('i.connections', 'con')
            ->where('con.module = :module')
            ->andWhere('con.other_id = :other_id')
            ->andWhere('i.language = :language')
            ->setParameter('module', (string) $module)
            ->setParameter('other_id', (int) $otherId)
            ->setParameter('language', $language)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get tags for multiple items.
     *
     * @param string $module   The module wherefore you want to retrieve the tags.
     * @param array  $otherIds The ids for the items.
     * @param string $language The language for the items.
     * @return Tag[] All tags for multiple items
     */
    public static function getForMultipleItems($module, array $otherIds, $language = null)
    {
        // redefine variables
        $language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;
        foreach ($otherIds as &$otherId) {
            $otherId = (int) $otherId;
        }

        return FrontendModel::get('doctrine.orm.entity_manager')
            ->getRepository(BackendTagsModel::ENTITY_CLASS)
            ->createQueryBuilder('i')
            ->leftJoin('i.connections', 'con')
            ->where('con.module = :module')
            ->andWhere('con.other_id IN (:other_id)')
            ->andWhere('i.language = :language')
            ->setParameter('module', (string) $module)
            ->setParameter('other_id', $otherIds)
            ->setParameter('language', $language)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get the tag-id for a given URL
     *
     * @param string $URL The URL to get the id for.
     * @return int
     */
    public static function getIdByURL($URL)
    {
        /** @var Tag $tag Retrieve the tag */
        $tag = self::get($URL);

        return ($tag) ? $tag->getId() : null;
    }

    /**
     * Get the modules that used a tag.
     *
     * @param int $id The id of the tag.
     * @return TagConnection[] All connections for a tag
     */
    public static function getModulesForTag($id)
    {
        return FrontendModel::get('doctrine.orm.entity_manager')
            ->getRepository(BackendTagsModel::ENTITY_CONNECTION_CLASS)
            ->createQueryBuilder('i')
            ->leftJoin('i.tag', 't')
            ->andWhere('t.id = :tag_id')
            ->setParameter('tag_id', (int) $id)
            ->orderBy('i.module', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Fetch a specific tag name
     *
     * @param int $id The id of the tag to grab the name for.
     * @return string
     */
    public static function getName($id)
    {
        /** @var Tag[] Retrieve a tag */
        $item = FrontendModel::get('doctrine.orm.entity_manager')
            ->getRepository(BackendTagsModel::ENTITY_CLASS)
            ->findOneBy(
                array(
                    'id'       => (int) $id,
                    'language' => $language,
                )
            )
        ;

        return ($item) ? $item->getName() : null;
    }

    /**
     * Get all related items
     *
     * @param int     $otherId     The id of the item in the source-module.
     * @param int     $module      The source module.
     * @param int     $otherModule The module wherein the related items should appear.
     * @param int $limit       The maximum of related items to grab.
     * @return TagConnection[] All connections for an item
     */
    public static function getRelatedItemsByTags($otherId, $module, $otherModule, $limit = 5)
    {
        return FrontendModel::get('doctrine.orm.entity_manager')
            ->getRepository(BackendTagsModel::ENTITY_CONNECTION_CLASS)
            ->createQueryBuilder('i')
            ->innerJoin(BackendTagsModel::ENTITY_CONNECTION_CLASS, 'i2', 'WITH', 'i.tag = i2.tag')
            ->where('i2.other_id = :other_id')
            ->andWhere('i2.module = :module')
            ->andWhere('i.other_id != :other_id')
            ->andWhere('i.module = :other_module')
            ->setParameter('other_id', (int) $otherId)
            ->setParameter('module', (string) $module)
            ->setParameter('other_module', (string) $otherModule)
            ->orderBy('COUNT(i.tag_id)', 'DESC')
            ->setMaxResults((int) $limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
