<?php

namespace Frontend\Modules\Blog\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with recent blog-articles
 */
class RecentArticlesList extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        // get RSS-link
        $rssTitle = $this->get('fork.settings')->get('Blog', 'rss_title_' . LANGUAGE, SITE_DEFAULT_TITLE);
        $rssLink = FrontendNavigation::getUrlForBlock('Blog', 'Rss');

        // add RSS-feed into the metaCustom
        $this->header->addRssLink($rssTitle, $rssLink);

        // assign comments
        $this->template->assign(
            'widgetBlogRecentArticlesList',
            FrontendBlogModel::getAll($this->get('fork.settings')->get('Blog', 'recent_articles_list_num_items', 5))
        );
        $this->template->assign('widgetBlogRecentArticlesFullRssLink', $rssLink);
    }
}
