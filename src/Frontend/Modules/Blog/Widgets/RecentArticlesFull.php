<?php

namespace Frontend\Modules\Blog\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with recent blog-articles
 */
class RecentArticlesFull extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        // get RSS-link
        $rssTitle = $this->get('fork.settings')->get('Blog', 'rss_title_' . LANGUAGE);
        $rssLink = FrontendNavigation::getURLForBlock('Blog', 'Rss');

        // add RSS-feed
        $this->header->addRssLink($rssTitle, $rssLink);

        // assign comments
        $this->tpl->assign(
            'widgetBlogRecentArticlesFull',
            FrontendBlogModel::getAll($this->get('fork.settings')->get('Blog', 'recent_articles_full_num_items', 5))
        );
        $this->tpl->assign('widgetBlogRecentArticlesFullRssLink', $rssLink);
    }
}
