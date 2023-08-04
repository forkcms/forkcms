<?php

namespace Frontend\Modules\Blog\Actions;

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
    public function execute(): void
    {
        parent::execute();

        $this->generateRss();
    }

    private function generateRss()
    {
        $blogPostComments = FrontendBlogModel::getAllComments();
        $rss = new FrontendRSS(
            \SpoonFilter::ucfirst(FL::msg('BlogAllComments')),
            SITE_URL . FrontendNavigation::getUrlForBlock($this->getModule()),
            ''
        );
        $blogPostUrlBase = SITE_URL . FrontendNavigation::getUrlForBlock($this->getModule(), 'Detail');

        foreach ($blogPostComments as $blogPostComment) {
            $rss->addItem($this->getRssFeedItemForBlogPostComment($blogPostComment, $blogPostUrlBase));
        }

        $rss->parse();
    }

    private function getRssFeedItemForBlogPostComment(
        array $blogPostComment,
        string $blogPostUrlBase
    ): FrontendRSSItem {
        $rssItem = new FrontendRSSItem(
            $blogPostComment['author'] . ' ' . FL::lbl('On') . ' ' . $blogPostComment['post_title'],
            $blogPostUrlBase . '/' . $blogPostComment['post_url'] . '/#comment-' . $blogPostComment['id'],
            $blogPostComment['text']
        );

        $rssItem->setPublicationDate($blogPostComment['created_on']);
        $rssItem->setAuthor(empty($blogPostComment['email']) ? $blogPostComment['author'] : $blogPostComment['email']);

        return $rssItem;
    }
}
