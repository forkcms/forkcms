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
class RecentArticlesList extends FrontendBaseWidget
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
        $rssTitle = $this->get('fork.settings')->get('Blog', 'rss_title_' . FRONTEND_LANGUAGE);
        $rssLink = FrontendNavigation::getURLForBlock('Blog', 'Rss');

        // add RSS-feed into the metaCustom
        $this->header->addRssLink($rssTitle, $rssLink);

        // assign comments
        $this->tpl->assign(
            'widgetBlogRecentArticlesList',
            FrontendBlogModel::getAll($this->get('fork.settings')->get('Blog', 'recent_articles_list_num_items', 5))
        );
        $this->tpl->assign('widgetBlogRecentArticlesFullRssLink', $rssLink);
    }
}
