<?php

namespace Frontend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Rss as FrontendRSS;
use Frontend\Core\Engine\RssItem as FrontendRSSItem;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the RSS-feed with all the comments
 */
class CommentsRss extends FrontendBaseBlock
{
    /**
     * The comments
     *
     * @var array
     */
    private $items;

    public function execute(): void
    {
        parent::execute();
        $this->getData();
        $this->parse();
    }

    private function getData(): void
    {
        $this->items = FrontendBlogModel::getAllComments();
    }

    private function parse(): void
    {
        $title = \SpoonFilter::ucfirst(FL::msg('BlogAllComments'));
        $link = SITE_URL . FrontendNavigation::getUrlForBlock('Blog');
        $detailLink = SITE_URL . FrontendNavigation::getUrlForBlock('Blog', 'Detail');
        $description = null;

        $rss = new FrontendRSS($title, $link, $description);

        // loop articles
        foreach ($this->items as $item) {
            $title = $item['author'] . ' ' . FL::lbl('On') . ' ' . $item['post_title'];
            $link = $detailLink . '/' . $item['post_url'] . '/#comment-' . $item['id'];
            $description = $item['text'];

            $rssItem = new FrontendRSSItem($title, $link, $description);

            $rssItem->setPublicationDate($item['created_on']);
            $rssItem->setAuthor($item['author']);

            $rss->addItem($rssItem);
        }

        $rss->parse();
    }
}
