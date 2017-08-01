<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use InvalidArgumentException;

/**
 * In this file we store all generic functions that we will be using in the frontend.
 */
class Model extends \Common\Core\Model
{
    /**
     * Visitor id from tracking cookie
     *
     * @var string
     */
    private static $visitorId;

    /**
     * Add parameters to an URL
     *
     * @param string $url The URL to append the parameters too.
     * @param array $parameters The parameters as key-value-pairs.
     *
     * @return string
     */
    public static function addUrlParameters(string $url, array $parameters): string
    {
        if (empty($parameters)) {
            return $url;
        }

        $chunks = explode('#', $url, 2);
        $hash = '';
        if (isset($chunks[1])) {
            $url = $chunks[0];
            $hash = '#' . $chunks[1];
        }

        // build query string
        $queryString = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
        if (mb_strpos($url, '?') !== false) {
            return $url . '&' . $queryString . $hash;
        }

        return $url . '?' . $queryString . $hash;
    }

    /**
     * Get plain text for a given text
     *
     * @param string $text The text to convert.
     * @param bool $includeAHrefs Should the url be appended after the link-text?
     * @param bool $includeImgAlts Should the alt tag be inserted for images?
     *
     * @return string
     */
    public static function convertToPlainText(string $text, bool $includeAHrefs = true, $includeImgAlts = true): string
    {
        // remove tabs, line feeds and carriage returns
        $text = str_replace(["\t", "\n", "\r"], '', $text);

        // remove the head-, style- and script-tags and all their contents
        $text = preg_replace('|\<head[^>]*\>(.*\n*)\</head\>|isU', '', $text);
        $text = preg_replace('|\<style[^>]*\>(.*\n*)\</style\>|isU', '', $text);
        $text = preg_replace('|\<script[^>]*\>(.*\n*)\</script\>|isU', '', $text);

        // put back some new lines where needed
        $text = preg_replace(
            '#(\<(h1|h2|h3|h4|h5|h6|p|ul|ol)[^\>]*\>.*\</(h1|h2|h3|h4|h5|h6|p|ul|ol)\>)#isU',
            "\n$1",
            $text
        );

        // replace br tags with newlines
        $text = preg_replace('#(\<br[^\>]*\>)#isU', "\n", $text);

        // replace links with the inner html of the link with the url between ()
        // eg.: <a href="http://site.domain.com">My site</a> => My site (http://site.domain.com)
        if ($includeAHrefs) {
            $text = preg_replace('|<a.*href="(.*)".*>(.*)</a>|isU', '$2 ($1)', $text);
        }

        // replace images with their alternative content
        // eg. <img src="path/to/the/image.jpg" alt="My image" /> => My image
        if ($includeImgAlts) {
            $text = preg_replace('|\<img[^>]*alt="(.*)".*/\>|isU', '$1', $text);
        }

        // decode html entities
        $text = html_entity_decode($text, ENT_QUOTES, 'ISO-8859-15');

        // remove space characters at the beginning and end of each line and clear lines with nothing but spaces
        $text = preg_replace('/^\s*|\s*$|^\s*$/m', '', $text);

        // strip tags
        $text = strip_tags($text, '<h1><h2><h3><h4><h5><h6><p><li>');

        // format heading, paragraphs and list items
        $text = preg_replace('|\<h[123456]([^\>]*)\>(.*)\</h[123456]\>|isU', "\n** $2 **\n", $text);
        $text = preg_replace('|\<p([^\>]*)\>(.*)\</p\>|isU', "$2\n", $text);
        $text = preg_replace('|\<li([^\>]*)\>\n*(.*)\n*\</li\>|isU', "- $2\n", $text);

        // replace 3 and more line breaks in a row by 2 line breaks
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        // use php constant for new lines
        $text = str_replace("\n", PHP_EOL, $text);

        // trim line breaks at the beginning and ending of the text
        $text = trim($text, PHP_EOL);

        // return the plain text
        return $text;
    }

    /**
     * Get all data for a page
     *
     * @param int $pageId The pageId wherefore the data will be retrieved.
     *
     * @return array
     */
    public static function getPage(int $pageId): array
    {
        // get data
        $revisionId = (int) self::getContainer()->get('database')->getVar(
            'SELECT p.revision_id
             FROM pages AS p
             WHERE p.id = ? AND p.status = ? AND p.language = ?
             LIMIT 1',
            [$pageId, 'active', LANGUAGE]
        );

        // No page found
        if ($revisionId === 0) {
            return [];
        }

        return self::getPageRevision($revisionId, false);
    }

    /**
     * Get a revision for a page
     *
     * @param int $revisionId The revisionID.
     * @param bool $allowHidden is used by the getPage method to prevent hidden records
     *
     * @return array
     */
    public static function getPageRevision(int $revisionId, bool $allowHidden = true): array
    {
        $pageRevision = (array) self::getContainer()->get('database')->getRecord(
            'SELECT p.id, p.parent_id, p.revision_id, p.template_id, p.title, p.navigation_title,
                 p.navigation_title_overwrite, p.data, p.hidden,
                 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
                 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
                 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
                 m.custom AS meta_custom,
                 m.url, m.url_overwrite,
                 m.data AS meta_data, m.seo_follow AS meta_seo_follow, m.seo_index AS meta_seo_index,
                 t.path AS template_path, t.data AS template_data
             FROM pages AS p
             INNER JOIN meta AS m ON p.meta_id = m.id
             INNER JOIN themes_templates AS t ON p.template_id = t.id
             WHERE p.revision_id = ? AND p.language = ?
             LIMIT 1',
            [$revisionId, LANGUAGE]
        );

        if (empty($pageRevision)) {
            return [];
        }

        if (!$allowHidden && (int) $pageRevision['id'] !== 404 && $pageRevision['hidden']) {
            return self::getPage(404);
        }

        $pageRevision = self::unserializeArrayContent($pageRevision, 'data');
        $pageRevision = self::unserializeArrayContent($pageRevision, 'meta_data');
        $pageRevision = self::unserializeArrayContent($pageRevision, 'template_data');
        $pageRevision['positions'] = self::getPositionsForRevision($pageRevision['revision_id'], $allowHidden);

        return $pageRevision;
    }

    /**
     * @param int $revisionId
     * @param bool $allowHidden
     *
     * @return array
     */
    private static function getPositionsForRevision(int $revisionId, bool $allowHidden = false): array
    {
        $positions = [];
        $where = 'pb.revision_id = ?';
        $parameters = [false, $revisionId];

        if (!$allowHidden) {
            $where .= ' AND p.status = ? AND pb.visible = ?';
            $parameters[] = 'active';
            $parameters[] = true;
        }

        // get blocks
        $blocks = (array) self::getContainer()->get('database')->getRecords(
            'SELECT pe.id AS extra_id, pb.html, pb.position,
             pe.module AS extra_module, pe.type AS extra_type, pe.action AS extra_action, pe.data AS extra_data
             FROM pages_blocks AS pb
             INNER JOIN pages AS p ON p.revision_id = pb.revision_id
             LEFT OUTER JOIN modules_extras AS pe ON pb.extra_id = pe.id AND pe.hidden = ?
             WHERE ' . $where . '
             ORDER BY pb.position, pb.sequence',
            $parameters
        );

        // loop blocks
        foreach ($blocks as $block) {
            $positions[$block['position']][] = self::unserializeArrayContent($block, 'data');
        }

        return $positions;
    }

    /**
     * Get the visitor's id (using a tracking cookie)
     *
     * @return string
     */
    public static function getVisitorId(): string
    {
        // check if tracking id is fetched already
        if (self::$visitorId !== null) {
            return self::$visitorId;
        }
        $cookie = self::getContainer()->get('fork.cookie');

        // get/init tracking identifier
        (self::$visitorId = $cookie->has('track') && $cookie->get('track', '') !== '')
            ? $cookie->get('track')
            : md5(uniqid('', true) . self::getSession()->getId());

        if ($cookie->hasAllowedCookies() || !self::get('fork.settings')->get('Core', 'show_cookie_bar', false)) {
            $cookie->set('track', self::$visitorId, 86400 * 365);
        }

        return self::getVisitorId();
    }

    /**
     * General method to check if something is spam
     *
     * @param string $content The content that was submitted.
     * @param string $permaLink The permanent location of the entry the comment was submitted to.
     * @param string $author Commenter's name.
     * @param string $email Commenter's email address.
     * @param string $url Commenter's URL.
     * @param string $type May be blank, comment, trackback, pingback, or a made up value like "registration".
     *
     * @throws \Exception
     *
     * @return bool|string Will return a boolean, except when we can't decide the status
     *                          (unknown will be returned in that case)
     */
    public static function isSpam(
        string $content,
        string $permaLink,
        string $author = null,
        string $email = null,
        string $url = null,
        string $type = 'comment'
    ) {
        try {
            $akismet = self::getAkismet();
        } catch (InvalidArgumentException $invalidArgumentException) {
            return false;
        }

        // try it, to decide if the item is spam
        try {
            // check with Akismet if the item is spam
            return $akismet->isSpam($content, $author, $email, $url, $permaLink, $type);
        } catch (\Exception $e) {
            // in debug mode we want to see exceptions, otherwise the fallback will be triggered
            if (self::getContainer()->getParameter('kernel.debug')) {
                throw $e;
            }

            // return unknown status
            return 'unknown';
        }
    }

    private static function unserializeArrayContent(array $array, string $key): array
    {
        if (isset($array[$key]) && $array[$key] !== '') {
            $array[$key] = unserialize($array[$key]);

            return $array;
        }

        return $array;
    }
}
