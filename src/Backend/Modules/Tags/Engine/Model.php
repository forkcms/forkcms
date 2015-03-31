<?php

namespace Backend\Modules\Tags\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;

use Backend\Modules\Tags\Entity\Tag;
use Backend\Modules\Tags\Entity\TagConnection;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * In this file we store all generic functions that we will be using in the TagsModule
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class Model
{
    const ENTITY_CLASS = 'Backend\Modules\Tags\Entity\Tag';
    const ENTITY_CONNECTION_CLASS = 'Backend\Modules\Tags\Entity\TagConnection';

    /**
     * Delete one or more tags.
     *
     * @param mixed $ids The ids to delete.
     */
    public static function delete(Tag $tag)
    {
        // delete the content_block
        $em = BackendModel::get('doctrine.orm.entity_manager');

        $em->remove($tag);
        $em->flush();
    }

    /**
     * Check if a tag exists.
     *
     * @param int $id The id to check for existence.
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) self::get($id);
    }

    /**
     * Check if a tag exists
     *
     * @param string $tag The tag to check for existence.
     * @return bool
     */
    public static function existsTag($tag)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em
            ->getRepository(self::ENTITY_CLASS)
            ->findOneBy(
                array(
                    'tag'       => (string) $tag,
                    'language' => BL::getWorkingLanguage(),
                )
            )
        ;
    }

    /**
     * Get tag record.
     *
     * @param int $id The id of the record to get.
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
                    'language' => BL::getWorkingLanguage(),
                )
            )
        ;
    }

    /**
     * Get tags that start with the given string
     *
     * @param string $term            The searchstring.
     * @param string $language        The language to use, if not provided
     *                                use the working language.
     * @return array
     */
    public static function getStartsWith($term, $language = null)
    {
        $language = ($language != null)
            ? (string) $language
            : BL::getWorkingLanguage();

        /** $var Tag[] Retrieve one tag */
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em
            ->getRepository(self::ENTITY_CLASS)
            ->createQueryBuilder('i')
            ->where('i.language = :language')
            ->andWhere('i.tag LIKE :term')
            ->setParameter('language', $language)
            ->setParameter('term', (string) $term . '%')
            ->orderBy('i.tag', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get tags for an item
     *
     * @param string $module   The module wherein will be searched.
     * @param int    $otherId  The id of the record.
     * @param string $type     The type of the returnvalue, possible values are: array, string (tags will be joined by ,).
     * @param string $language The language to use, if not provided the working language will be used.
     * @return mixed
     */
    public static function getTags($module, $otherId, $type = 'string', $language = null)
    {
        $module = (string) $module;
        $otherId = (int) $otherId;
        $type = (string) \SpoonFilter::getValue($type, array('string', 'array'), 'string');
        $language = ($language != null) ? (string) $language : BL::getWorkingLanguage();

        /** @var Array $items Retrieve the list of only the 'tag' field */
        $items = BackendModel::get('doctrine.orm.entity_manager')
            ->getRepository(self::ENTITY_CLASS)
            ->createQueryBuilder('i')
            ->leftJoin('i.connections', 'con')
            ->where('con.module = :module')
            ->andWhere('con.other_id = :other_id')
            ->andWhere('i.language = :language')
            ->setParameter('module', $module)
            ->setParameter('other_id', $otherId)
            ->setParameter('language', $language)
            ->getQuery()
            ->getResult()
        ;

        $results = array();
        foreach ($items as $item) {
            $results[] = $item->getTag();
        }

        // return as an imploded string
        if ($type == 'string') {
            return implode(',', $results);
        }

        // return as array
        return $results;
    }

    /**
     * Get a unique URL for a tag
     *
     * @param string $URL The URL to use as a base.
     * @param int    $id  The ID to ignore.
     * @return string
     */
    public static function getURL($URL, $id = null)
    {
        $URL = CommonUri::getUrl((string) $URL);
        $language = BL::getWorkingLanguage();

        $em = BackendModel::get('doctrine.orm.entity_manager');

        // no specific id
        if ($id === null) {
            $items = $em
                ->getRepository(self::ENTITY_CLASS)
                ->findBy(array(
                     'url' => $URL,
                     'language' => $language
                ))
            ;

            // there are items so, call this method again.
            if (!empty($items)) {
                // add a number
                $URL = BackendModel::addNumber($URL);

                // recall this method, but with a new url
                $URL = self::getURL($URL, $id);
            }
        } else {
            $items = $em
                ->getRepository(self::ENTITY_CLASS)
                ->createQueryBuilder('i')
                ->where('i.url = :url')
                ->andWhere('i.language = :language')
                ->andWhere('i.id != :id')
                ->setParameter('url', $URL)
                ->setParameter('language', $language)
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult()
            ;

            // there are items so, call this method again.
            if (!empty($items)) {
                // add a number
                $URL = BackendModel::addNumber($URL);

                // recall this method, but with a new url
                $URL = self::getURL($URL, $id);
            }
        }

        return $URL;
    }

    /**
     * Insert a new tag
     *
     * @param string $tag      The data for the tag.
     * @param string $language The language wherein the tag will be inserted,
     *                         if not provided the workinglanguage will be used.
     * @return int
     */
    public static function insert(Tag $item)
    {
        // insert tag
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($item);
        $em->flush();

        return $item->getId();
    }

    /**
     * Save the tags
     *
     * @param int    $otherId  The id of the item to tag.
     * @param mixed  $tags     The tags for the item.
     * @param string $module   The module wherein the item is located.
     * @param string $language The language wherein the tags will be inserted,
     *                         if not provided the workinglanguage will be used.
     */
    public static function saveTags($otherId, $tags, $module, $language = null)
    {
        // redefine variables
        $otherId = (int) $otherId;
        $tags = (is_array($tags)) ? (array) $tags : (string) $tags;
        $module = (string) $module;
        $language = ($language != null) ? (string) $language : BL::getWorkingLanguage();

        // get entity manager
        $em = BackendModel::get('doctrine.orm.entity_manager');

        /** @var Tag[] $oldConnections Retrieve the old connections for the item */
        $oldConnections = $em
            ->getRepository(self::ENTITY_CONNECTION_CLASS)
            ->createQueryBuilder('i')
            ->leftJoin('i.tag', 't')
            ->where('i.module = :module')
            ->andWhere('i.other_id = :other_id')
            ->andWhere('t.language = :language')
            ->setParameter('module', $module)
            ->setParameter('other_id', $otherId)
            ->setParameter('language', $language)
            ->getQuery()
            ->getResult()
        ;

        // we had connections, so we delete all these connections
        if (!empty($oldConnections)) {
            foreach ($oldConnections as $connection) {
                // remove connection
                $em->remove($connection);

                // define the connected tag
                $tag = $connection->getTag();

                // decrease number
                $number = $tag->getNumber() - 1;
                $tag->setNumber(($number > 0) ? $number : 0);

                // update tag
                $em->persist($tag);
            }

            $em->flush();
        }

        // redefine the tags as an array
        if (!is_array($tags)) {
            $tags = (array) explode(',', $tags);
        }

        // make sure the list of tags is unique
        $tags = array_unique($tags); 
        
        // we have tags to save
        if (!empty($tags)) {
            foreach ($tags as $key => $tag) {
                // cleanup
                $tag = strtolower(trim($tag));

                // unset if the tag is empty
                if ($tag == '') {
                    unset($tags[$key]);
                } else {
                    $tags[$key] = (string) $tag;
                }
            }

            /** @var Tag[] $tagsToConnect Retrieve all tags which this item will connect to */
            $tagsToConnect = $em
                ->getRepository(self::ENTITY_CLASS)
                ->findBy(array(
                    'tag' => $tags,
                    'language' => $language
                ))
            ;

            $existingTagIds = array();

            // loop all tags to connect
            foreach ($tagsToConnect as $tag) {
                $existingTagIds[$tag->getTag()] = $tag->getId();
            }

            // loop again and insert tags that don't already exist
            foreach ($tags as $tag) {
                // tag doesn't exist yet
                if (!isset($existingTagIds[$tag])) {            
                    // build new tag
                    $item = new Tag();
                    $item
                        ->setTag($tag)
                        ->setNumber(0)
                        ->setUrl(self::getURL($tag))
                        ->setLanguage($language)
                    ;

                    // insert tag
                    self::insert($item);

                    // add to tags to connect
                    $tagsToConnect[] = $item;
                }
            }

            // loop all tags to connect
            foreach ($tagsToConnect as $tag) {
                // build new tag connection
                $item = new TagConnection();
                $item
                    ->setTag($tag)
                    ->setModule($module)
                    ->setOtherId($otherId)
                ;

                // add connection
                $tag->addConnection($item);

                // bump number
                $tag->setNumber($tag->getNumber() + 1);

                // update
                $em->persist($item);
                $em->persist($tag);
            }
            $em->flush();
        }

        // add to search index
        BackendSearchModel::saveIndex(
            $module,
            $otherId,
            array(
                'tags' => implode(' ', (array) $tags)
            ),
            $language
        );

        // remove all tags that don't have anything linked
        $em
            ->createQuery('delete from ' . self::ENTITY_CLASS . ' i where i.number = 0')
            ->execute()
        ;
    }

    /**
     * Update a tag
     *
     * @param Tag $item The new data.
     */
    public static function update(Tag $tag)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        // update tag
        $em->persist($tag);
        $em->flush();

        return $tag->getId();
    }
}
