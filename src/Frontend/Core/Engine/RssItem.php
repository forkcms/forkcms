<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;

/**
 * FrontendRSSItem, this is our extended version of SpoonRSSItem
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 */
class RssItem extends \SpoonFeedRSSItem
{
    /**
     * Initial values for UTM-parameters
     *
     * @var    array
     */
    private $utm = array('utm_source' => 'feed', 'utm_medium' => 'rss');

    /**
     * @param string $title       The title for the item.
     * @param string $link        The link for the item.
     * @param string $description The content for the item.
     */
    public function __construct($title, $link, $description)
    {
        // decode
        $title = \SpoonFilter::htmlspecialcharsDecode($title);
        $description = \SpoonFilter::htmlspecialcharsDecode($description);

        // set UTM-campaign
        $this->utm['utm_campaign'] = CommonUri::getUrl($title);

        // call parent
        parent::__construct($title, Model::addURLParameters($link, $this->utm), $description);

        // set some properties
        $this->setGuid($link, true);
    }

    /**
     * Process links, will prepend SITE_URL if needed and append UTM-parameters
     *
     * @param string $content The content to process.
     * @return string
     */
    public function processLinks($content)
    {
        // redefine
        $content = (string) $content;

        // replace URLs and images
        $search = array('href="/', 'src="/');
        $replace = array('href="' . SITE_URL . '/', 'src="' . SITE_URL . '/');

        // replace links to files
        $content = str_replace($search, $replace, $content);

        // init var
        $matches = array();

        // match links
        preg_match_all('/href="(http:\/\/(.*))"/iU', $content, $matches);

        // any links?
        if (isset($matches[1]) && !empty($matches[1])) {
            // init vars
            $searchLinks = array();
            $replaceLinks = array();

            // loop old links
            foreach ($matches[1] as $i => $link) {
                $searchLinks[] = $matches[0][$i];
                $replaceLinks[] = 'href="' . Model::addURLParameters($link, $this->utm) . '"';
            }

            // replace
            $content = str_replace($searchLinks, $replaceLinks, $content);
        }

        return $content;
    }

    /**
     * Set the author.
     *
     * @param string $author The author to use.
     */
    public function setAuthor($author)
    {
        // remove special chars
        $author = (string) \SpoonFilter::htmlspecialcharsDecode($author);

        // add fake-emailaddress
        if (!\SpoonFilter::isEmail($author)) {
            $author = CommonUri::getUrl($author) . '@example.com (' . $author . ')';
        }
        // add fake email address
        if (!\SpoonFilter::isEmail($author)) {
            $author = \SpoonFilter::urlise($author) . '@example.com (' . $author . ')';
        }

        // set author
        parent::setAuthor($author);
    }

    /**
     * Set the description.
     * All links and images that link to internal files will be prepended with the sites URL
     *
     * @param string $description The content of the item.
     */
    public function setDescription($description)
    {
        // remove special chars
        $description = (string) \SpoonFilter::htmlspecialcharsDecode($description);

        // process links
        $description = $this->processLinks($description);

        // call parent
        parent::setDescription($description);
    }

    /**
     * Set the guid.
     * If the link is an internal link the sites URL will be prepended.
     *
     * @param string $link        The guid for an item.
     * @param bool   $isPermaLink Is this link permanent?
     */
    public function setGuid($link, $isPermaLink = true)
    {
        // redefine var
        $link = (string) $link;

        // if link doesn't start with http, we prepend the URL of the site
        if (substr($link, 0, 7) != 'http://') {
            $link = SITE_URL . $link;
        }

        // call parent
        parent::setGuid($link, $isPermaLink);
    }

    /**
     * Set the link.
     * If the link is an internal link the sites URL will be prepended.
     *
     * @param string $link The link for the item.
     */
    public function setLink($link)
    {
        // redefine var
        $link = (string) $link;

        // if link doesn't start with http, we prepend the URL of the site
        if (substr($link, 0, 7) != 'http://') {
            $link = SITE_URL . $link;
        }

        // call parent
        parent::setLink($link);
    }
}
