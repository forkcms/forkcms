<?php

namespace Frontend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Rss as FrontendRSS;
use Frontend\Core\Engine\RssItem as FrontendRSSItem;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the RSS-feed for comments on a certain article.
 */
class ArticleCommentsRss extends FrontendBaseBlock
{
    /**
     * The record
     *
     * @var array
     */
    private $record;

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
        // validate incoming parameters
        if ($this->url->getParameter(1) === null) {
            $this->redirect(FrontendNavigation::getUrl(404));
        }

        // get record
        $this->record = FrontendBlogModel::get($this->url->getParameter(1));

        // anything found?
        if (empty($this->record)) {
            $this->redirect(FrontendNavigation::getUrl(404));
        }

        // get articles
        $this->items = FrontendBlogModel::getComments($this->record['id']);
    }

    private function parse(): void
    {
        // get vars
        $title = vsprintf(FL::msg('CommentsOn'), [$this->record['title']]);
        $link = SITE_URL . FrontendNavigation::getUrlForBlock('Blog', 'ArticleCommentsRss') .
                '/' . $this->record['url'];
        $detailLink = SITE_URL . FrontendNavigation::getUrlForBlock('Blog', 'Detail');
        $description = null;

        // create new rss instance
        $rss = new FrontendRSS($title, $link, $description);

        // loop articles
        foreach ($this->items as $item) {
            $title = $item['author'] . ' ' . FL::lbl('On') . ' ' . $this->record['title'];
            $link = $detailLink . '/' . $this->record['url'] . '/#comment-' . $item['id'];
            $description = $item['text'];

            $rssItem = new FrontendRSSItem($title, $link, $description);

            $rssItem->setPublicationDate($item['created_on']);
            $rssItem->setAuthor($item['author']);

            $rss->addItem($rssItem);
        }

        $rss->parse();
    }
}
