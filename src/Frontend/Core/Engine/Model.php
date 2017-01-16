<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use TijsVerkoyen\Akismet\Akismet;
use Common\Cookie as CommonCookie;

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
     * @param string $url        The URL to append the parameters too.
     * @param array  $parameters The parameters as key-value-pairs.
     *
     * @return string
     */
    public static function addURLParameters($url, array $parameters)
    {
        $url = (string) $url;

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
        $queryString = http_build_query($parameters, null, '&amp;', PHP_QUERY_RFC3986);
        if (mb_strpos($url, '?') !== false) {
            $url .= '&' . $queryString . $hash;
        } else {
            $url .= '?' . $queryString . $hash;
        }

        return $url;
    }

    /**
     * Get plain text for a given text
     *
     * @param string $text           The text to convert.
     * @param bool   $includeAHrefs  Should the url be appended after the link-text?
     * @param bool   $includeImgAlts Should the alt tag be inserted for images?
     *
     * @return string
     */
    public static function convertToPlainText($text, $includeAHrefs = true, $includeImgAlts = true)
    {
        // remove tabs, line feeds and carriage returns
        $text = str_replace(array("\t", "\n", "\r"), '', $text);

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
    public static function getPage($pageId)
    {
        // redefine
        $pageId = (int) $pageId;

        // get database instance
        $db = self::getContainer()->get('database');

        // get data
        $record = (array) $db->getRecord(
            'SELECT p.id, p.parent_id, p.revision_id, p.template_id, p.title, p.navigation_title,
                 p.navigation_title_overwrite, p.data, p.hidden,
                 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
                 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
                 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
                 m.custom AS meta_custom,
                 m.url, m.url_overwrite,
                 m.data AS meta_data,
                 t.path AS template_path, t.data AS template_data
             FROM pages AS p
             INNER JOIN meta AS m ON p.meta_id = m.id
             INNER JOIN themes_templates AS t ON p.template_id = t.id
             WHERE p.id = ? AND p.status = ? AND p.language = ?
             LIMIT 1',
            array($pageId, 'active', LANGUAGE)
        );

        // validate
        if (empty($record)) {
            return array();
        }

        // if the page is hidden we need a 404 record
        if ($record['hidden'] === 'Y' && $pageId !== 404) {
            return self::getPage(404);
        }

        // unserialize page data and template data
        if (isset($record['data']) && $record['data'] != '') {
            $record['data'] = unserialize($record['data']);
        }
        if (isset($record['meta_data']) && $record['meta_data'] != '') {
            $record['meta_data'] = unserialize(
                $record['meta_data']
            );
        }
        if (isset($record['template_data']) && $record['template_data'] != '') {
            $record['template_data'] = @unserialize(
                $record['template_data']
            );
        }

        // get blocks
        $blocks = (array) $db->getRecords(
            'SELECT pe.id AS extra_id, pb.html, pb.position,
             pe.module AS extra_module, pe.type AS extra_type, pe.action AS extra_action, pe.data AS extra_data
             FROM pages_blocks AS pb
             INNER JOIN pages AS p ON p.revision_id = pb.revision_id
             LEFT OUTER JOIN modules_extras AS pe ON pb.extra_id = pe.id AND pe.hidden = ?
             WHERE pb.revision_id = ? AND p.status = ? AND pb.visible = ?
             ORDER BY pb.position, pb.sequence',
            array('N', $record['revision_id'], 'active', 'Y')
        );

        // init positions
        $record['positions'] = array();

        // loop blocks
        foreach ($blocks as $block) {
            // unserialize data if it is available
            if (isset($block['data'])) {
                $block['data'] = unserialize($block['data']);
            }

            // save to position
            $record['positions'][$block['position']][] = $block;
        }

        return $record;
    }

    /**
     * Get a revision for a page
     *
     * @param int $revisionId The revisionID.
     *
     * @return array
     */
    public static function getPageRevision($revisionId)
    {
        $revisionId = (int) $revisionId;

        // get database instance
        $db = self::getContainer()->get('database');

        // get data
        $record = (array) $db->getRecord(
            'SELECT p.id, p.parent_id, p.revision_id, p.template_id, p.title, p.navigation_title, p.navigation_title_overwrite,
                 p.data,
                 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
                 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
                 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
                 m.custom AS meta_custom,
                 m.url, m.url_overwrite,
                 t.path AS template_path, t.data AS template_data
             FROM pages AS p
             INNER JOIN meta AS m ON p.meta_id = m.id
             INNER JOIN themes_templates AS t ON p.template_id = t.id
             WHERE p.revision_id = ? AND p.language = ?
             LIMIT 1',
            array($revisionId, LANGUAGE)
        );

        // validate
        if (empty($record)) {
            return array();
        }

        // unserialize page data and template data
        if (isset($record['data']) && $record['data'] != '') {
            $record['data'] = unserialize($record['data']);
        }
        if (isset($record['template_data']) && $record['template_data'] != '') {
            $record['template_data'] = @unserialize(
                $record['template_data']
            );
        }

        // get blocks
        $blocks = (array) $db->getRecords(
            'SELECT pe.id AS extra_id, pb.html, pb.position,
             pe.module AS extra_module, pe.type AS extra_type, pe.action AS extra_action, pe.data AS extra_data
             FROM pages_blocks AS pb
             INNER JOIN pages AS p ON p.revision_id = pb.revision_id
             LEFT OUTER JOIN modules_extras AS pe ON pb.extra_id = pe.id AND pe.hidden = ?
             WHERE pb.revision_id = ?
             ORDER BY pb.position, pb.sequence',
            array('N', $record['revision_id'])
        );

        // init positions
        $record['positions'] = array();

        // loop blocks
        foreach ($blocks as $block) {
            // unserialize data if it is available
            if (isset($block['data'])) {
                $block['data'] = unserialize($block['data']);
            }

            // save to position
            $record['positions'][$block['position']][] = $block;
        }

        return $record;
    }

    /**
     * Get the visitor's id (using a tracking cookie)
     *
     * @return string
     */
    public static function getVisitorId()
    {
        // check if tracking id is fetched already
        if (self::$visitorId !== null) {
            return self::$visitorId;
        }

        // get/init tracking identifier
        self::$visitorId = CommonCookie::exists('track') && !empty($_COOKIE['track'])
            ? (string) CommonCookie::get('track')
            : md5(uniqid('', true) . \SpoonSession::getSessionId());

        if (!self::get('fork.settings')->get('Core', 'show_cookie_bar', false) || CommonCookie::hasAllowedCookies()) {
            CommonCookie::set('track', self::$visitorId, 86400 * 365);
        }

        return self::getVisitorId();
    }

    /**
     * General method to check if something is spam
     *
     * @param string $content   The content that was submitted.
     * @param string $permaLink The permanent location of the entry the comment was submitted to.
     * @param string $author    Commenter's name.
     * @param string $email     Commenter's email address.
     * @param string $url       Commenter's URL.
     * @param string $type      May be blank, comment, trackback, pingback, or a made up value like "registration".
     *
     * @return bool|string Will return a boolean, except when we can't decide the status
     *                          (unknown will be returned in that case)
     * @throws \Exception
     */
    public static function isSpam($content, $permaLink, $author = null, $email = null, $url = null, $type = 'comment')
    {
        // get some settings
        $akismetKey = self::get('fork.settings')->get('Core', 'akismet_key');

        // invalid key, so we can't detect spam
        if ($akismetKey === '') {
            return false;
        }

        // create new instance
        $akismet = new Akismet($akismetKey, SITE_URL);

        // set properties
        $akismet->setTimeOut(10);
        $akismet->setUserAgent('Fork CMS/' . FORK_VERSION);

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
}
