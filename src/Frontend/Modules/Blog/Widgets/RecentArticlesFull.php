<?php

namespace Frontend\Modules\Blog\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with recent blog-articles
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
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
        $rssLink = FrontendNavigation::getURLForBlock('Blog', 'Rss');

        // add RSS-feed
        $this->header->addLink(
            array(
                 'rel' => 'alternate',
                 'type' => 'application/rss+xml',
                 'title' => FrontendModel::getModuleSetting('Blog', 'rss_title_' . FRONTEND_LANGUAGE),
                 'href' => $rssLink
            ),
            true
        );

        // assign comments
        $this->tpl->assign(
            'widgetBlogRecentArticlesFull',
            FrontendBlogModel::getAll(FrontendModel::getModuleSetting('Blog', 'recent_articles_full_num_items', 5))
        );
        $this->tpl->assign('widgetBlogRecentArticlesFullRssLink', $rssLink);
    }
}
