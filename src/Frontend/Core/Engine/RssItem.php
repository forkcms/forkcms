<?php

namespace Frontend\Core\Engine;

use Common\Uri as CommonUri;

/**
 * FrontendRSSItem, this is our extended version of SpoonRSSItem
 */
class RssItem extends \SpoonFeedRSSItem
{
    /**
     * Initial values for UTM-parameters
     *
     * @var array
     */
    private $utm = ['utm_source' => 'feed', 'utm_medium' => 'rss'];

    public function __construct(string $title, string $link, string $content)
    {
        // decode
        $title = \SpoonFilter::htmlspecialcharsDecode($title);
        $content = \SpoonFilter::htmlspecialcharsDecode($content);

        // set UTM-campaign
        $this->utm['utm_campaign'] = CommonUri::getUrl($title);

        // call parent
        parent::__construct($title, Model::addUrlParameters($link, $this->utm, '&amp;'), $content);

        // set some properties
        $this->setGuid($link, true);
    }

    /**
     * Process links, will prepend SITE_URL if needed and append UTM-parameters
     *
     * @param string $content The content to process.
     *
     * @return string
     */
    public function processLinks(string $content): string
    {
        // replace URLs and images
        $search = ['href="/', 'src="/'];
        $replace = ['href="' . SITE_URL . '/', 'src="' . SITE_URL . '/'];

        // replace links to files
        $content = str_replace($search, $replace, $content);

        // init var
        $matches = [];

        // match links
        preg_match_all('/href="(http:\/\/(.*))"/iU', $content, $matches);

        // any links?
        if (!isset($matches[1]) || empty($matches[1])) {
            return $content;
        }

        $searchLinks = [];
        $replaceLinks = [];

        // loop old links
        foreach ((array) $matches[1] as $i => $link) {
            $searchLinks[] = $matches[0][$i];
            $replaceLinks[] = 'href="' . Model::addUrlParameters($link, $this->utm, '&amp;') . '"';
        }

        // replace
        return str_replace($searchLinks, $replaceLinks, $content);
    }

    public function setAuthor($author): void
    {
        // remove special chars
        $author = (string) \SpoonFilter::htmlspecialcharsDecode($author);

        // add fake emailaddress
        if (!filter_var($author, FILTER_VALIDATE_EMAIL)) {
            $author = CommonUri::getUrl($author) . '@example.com (' . $author . ')';
        }

        // set author
        parent::setAuthor($author);
    }

    /**
     * All links and images that link to internal files will be prepended with the sites URL
     *
     * @param string $description The content of the item.
     */
    public function setDescription($description): void
    {
        // remove special chars
        $description = (string) \SpoonFilter::htmlspecialcharsDecode($description);

        // process links
        $description = $this->processLinks($description);

        // call parent
        parent::setDescription($description);
    }

    /**
     * If the link is an internal link the sites URL will be prepended.
     *
     * @param string $link The guid for an item.
     * @param bool $isPermaLink Is this link permanent?
     */
    public function setGuid($link, $isPermaLink = true): void
    {
        parent::setGuid($this->prependWithSiteUrlIfHttpIsMissing($link), $isPermaLink);
    }

    private function prependWithSiteUrlIfHttpIsMissing(string $link): string
    {
        // if link doesn't start with http(s), we prepend the URL of the site
        if (!Model::getContainer()->get('fork.validator.url')->isExternalUrl($link)) {
            return SITE_URL . $link;
        }

        return $link;
    }

    /**
     * If the link is an internal link the sites URL will be prepended.
     *
     * @param string $link The link for the item.
     */
    public function setLink($link): void
    {
        parent::setLink($this->prependWithSiteUrlIfHttpIsMissing($link));
    }
}
