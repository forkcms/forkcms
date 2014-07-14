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
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the overview-action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Index extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var    array
     */
    private $items;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        // requested page
        $requestedPage = $this->URL->getParameter('page', 'int', 1);

        $allItems = FrontendBlogModel::getAll(0);

        $blogUrl = FrontendNavigation::getURLForBlock('Blog');

        $this->items = $this->parsePagination(
            $allItems,
            function($page) use ($blogUrl) {
                return $blogUrl . '?page=' . $page;
            },
            $requestedPage,
            FrontendModel::getModuleSetting('Blog', 'overview_num_items', 10)
        );
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // get RSS-link
        $rssLink = FrontendModel::getModuleSetting('Blog', 'feedburner_url_' . FRONTEND_LANGUAGE);
        if ($rssLink == '') {
            $rssLink = FrontendNavigation::getURLForBlock('Blog', 'Rss');
        }

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

        // assign articles
        $this->tpl->assign('items', $this->items);
    }
}
