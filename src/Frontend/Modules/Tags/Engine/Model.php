<?php

namespace Frontend\Modules\Tags\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Locale;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Language\Locale as FrontendLocale;

/**
 * In this file we store all generic functions that we will be using in the tags module
 */
class Model
{
    /**
     * Calls a method that has to be implemented though the tags interface
     *
     * @param string $module The module wherein to search.
     * @param string $class The class that should contain the method.
     * @param string $method The method to call.
     * @param mixed $parameter The parameters to pass.
     *
     * @throws FrontendException When FrontendTagsInterface is not correctly implemented to the module model
     *
     * @return mixed
     */
    public static function callFromInterface(string $module, string $class, string $method, $parameter = null)
    {
        // check to see if the interface is implemented
        if (in_array('Frontend\\Modules\\Tags\\Engine\\TagsInterface', class_implements($class))) {
            // return result
            return call_user_func([$class, $method], $parameter);
        }

        throw new FrontendException(
            'To use the tags module you need
            to implement the FrontendTagsInterface
            in the model of your module
            (' . $module . ').'
        );
    }

    public static function get(string $url, Locale $locale = null): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT id, language, tag AS name, number, url
             FROM tags
             WHERE url = ? AND language = ?',
            [$url, $locale ?? FrontendLocale::frontendLanguage()]
        );
    }

    /**
     * Fetch the list of all tags, ordered by their occurrence
     *
     * @return array
     */
    public static function getAll(): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT t.tag AS name, t.url, t.number
             FROM tags AS t
             WHERE t.language = ? AND t.number > 0
             ORDER BY t.tag',
            [FrontendLocale::frontendLanguage()]
        );
    }

    /**
     * @param string $module The module wherein the otherId occurs.
     * @param int $otherId The id of the item.
     * @param Locale|null $locale
     *
     * @return array
     */
    public static function getForItem(string $module, int $otherId, Locale $locale = null): array
    {
        $return = [];

        // get tags
        $linkedTags = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT t.tag AS name, t.url
             FROM modules_tags AS mt
             INNER JOIN tags AS t ON mt.tag_id = t.id
             WHERE mt.module = ? AND mt.other_id = ? AND t.language = ?',
            [$module, $otherId, $locale ?? FrontendLocale::frontendLanguage()]
        );

        // return
        if (empty($linkedTags)) {
            return $return;
        }

        // create link
        $tagLink = FrontendNavigation::getUrlForBlock('Tags', 'Detail');

        // loop tags
        foreach ($linkedTags as $row) {
            // add full URL
            $row['full_url'] = $tagLink . '/' . $row['url'];

            // add
            $return[] = $row;
        }

        // return
        return $return;
    }

    /**
     * Get tags for multiple items.
     *
     * @param string $module The module wherefore you want to retrieve the tags.
     * @param array $otherIds The ids for the items.
     * @param Locale|null $locale
     *
     * @return array
     */
    public static function getForMultipleItems(string $module, array $otherIds, Locale $locale = null): array
    {
        $database = FrontendModel::getContainer()->get('database');

        // init var
        $return = [];

        // get tags
        $linkedTags = (array) $database->getRecords(
            'SELECT mt.other_id, t.tag AS name, t.url
             FROM modules_tags AS mt
             INNER JOIN tags AS t ON mt.tag_id = t.id
             WHERE mt.module = ? AND t.language = ? AND mt.other_id IN (' . implode(', ', $otherIds) . ')',
            [$module, $locale ?? FrontendLocale::frontendLanguage()]
        );

        // return
        if (empty($linkedTags)) {
            return $return;
        }

        // create link
        $tagLink = FrontendNavigation::getUrlForBlock('Tags', 'Detail');

        // loop tags
        foreach ($linkedTags as $row) {
            // add full URL
            $row['full_url'] = $tagLink . '/' . $row['url'];

            // add
            $return[$row['other_id']][] = $row;
        }

        return $return;
    }

    public static function getIdByUrl(string $url): int
    {
        return (int) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT id
             FROM tags
             WHERE url = ?',
            [$url]
        );
    }

    public static function getModulesForTag(int $tagId): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getColumn(
            'SELECT module
             FROM modules_tags
             WHERE tag_id = ?
             GROUP BY module
             ORDER BY module ASC',
            [$tagId]
        );
    }

    public static function getName(int $tagId): string
    {
        return FrontendModel::getContainer()->get('database')->getVar(
            'SELECT tag
             FROM tags
             WHERE id = ?',
            [$tagId]
        );
    }

    /**
     * Get all related items
     *
     * @param int $id The id of the item in the source-module.
     * @param string $module The source module.
     * @param string $otherModule The module wherein the related items should appear.
     * @param int $limit The maximum of related items to grab.
     *
     * @return array
     */
    public static function getRelatedItemsByTags(int $id, string $module, string $otherModule, int $limit = 5): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getColumn(
            'SELECT t2.other_id
             FROM modules_tags AS t
             INNER JOIN modules_tags AS t2 ON t.tag_id = t2.tag_id
             WHERE t.other_id = ? AND t.module = ? AND t2.module = ? AND
                (t2.module != t.module OR t2.other_id != t.other_id)
             GROUP BY t2.other_id
             ORDER BY COUNT(t2.tag_id) DESC
             LIMIT ?',
            [$id, $module, $otherModule, $limit]
        );
    }
}
