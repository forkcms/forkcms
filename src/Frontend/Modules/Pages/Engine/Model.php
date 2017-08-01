<?php

namespace Frontend\Modules\Pages\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Url as FrontendUrl;
use Frontend\Modules\Tags\Engine\TagsInterface as FrontendTagsInterface;

/**
 * In this file we store all generic functions that we will be using in the pages module
 */
class Model implements FrontendTagsInterface
{
    /**
     * Fetch a list of items for a list of ids
     *
     * @param array $ids The ids of the items to grab.
     *
     * @return array
     */
    public static function getForTags(array $ids): array
    {
        // fetch items
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.title
             FROM pages AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.status = ? AND i.hidden = ? AND i.language = ? AND i.publish_on <= ? AND i.id IN (' .
            implode(',', $ids) . ')
             ORDER BY i.title ASC',
            ['active', false, LANGUAGE, FrontendModel::getUTCDate('Y-m-d H:i') . ':00']
        );

        // has items
        if (!empty($items)) {
            // reset url
            foreach ($items as &$row) {
                $row['full_url'] = FrontendNavigation::getUrl($row['id'], LANGUAGE);
            }
        }

        // return
        return $items;
    }

    /**
     * Get the id of an item by the full URL of the current page.
     * Selects the proper part of the full URL to get the item's id from the database.
     *
     * @param FrontendUrl $url The current URL.
     *
     * @return int
     */
    public static function getIdForTags(FrontendUrl $url): int
    {
        return FrontendNavigation::getPageId($url->getQueryString());
    }

    /**
     * Fetch a list of subpages of a page.
     *
     * @param int $id The id of the item to grab the subpages for.
     *
     * @return array
     */
    public static function getSubpages(int $id): array
    {
        // fetch items
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.title, m.description, i.parent_id, i.data
             FROM pages AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.parent_id = ? AND i.status = ? AND i.hidden = ?
             AND i.language = ? AND i.publish_on <= ?
             ORDER BY i.sequence ASC',
            [$id, 'active', false, LANGUAGE, FrontendModel::getUTCDate('Y-m-d H:i') . ':00']
        );

        // has items
        if (!empty($items)) {
            foreach ($items as &$row) {
                // reset url
                $row['full_url'] = FrontendNavigation::getUrl($row['id'], LANGUAGE);

                // unserialize page data and template data
                if (!empty($row['data'])) {
                    $row['data'] = unserialize($row['data']);
                }
            }
        }

        // return
        return $items;
    }

    /**
     * Parse the search results for this module
     *
     * Note: a module's search function should always:
     *        - accept an array of entry id's
     *        - return only the entries that are allowed to be displayed, with their array's index being the entry's id
     *
     * @param array $ids The ids of the found results.
     *
     * @return array
     */
    public static function search(array $ids): array
    {
        // get database
        $database = FrontendModel::getContainer()->get('database');

        // define ids to ignore
        $ignore = [404];

        // get items
        $items = (array) $database->getRecords(
            'SELECT p.id, p.title, m.url, p.revision_id AS text
             FROM pages AS p
             INNER JOIN meta AS m ON p.meta_id = m.id
             INNER JOIN themes_templates AS t ON p.template_id = t.id
             WHERE p.id IN (' . implode(', ', $ids) . ') AND p.id NOT IN (' .
            implode(', ', $ignore) . ') AND p.status = ? AND p.hidden = ? AND p.language = ?',
            ['active', false, LANGUAGE],
            'id'
        );

        // prepare items for search
        foreach ($items as &$item) {
            $item['text'] = implode(
                ' ',
                (array) $database->getColumn(
                    'SELECT pb.html
                     FROM pages_blocks AS pb
                     WHERE pb.revision_id = ?',
                    [$item['text']]
                )
            );

            $item['full_url'] = FrontendNavigation::getUrl($item['id']);
        }

        return $items;
    }
}
