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
     * The pagination array
     * It will hold all needed parameters, some of them need initialization.
     *
     * @var    array
     */
    protected $pagination = array(
        'limit' => 10,
        'offset' => 0,
        'requested_page' => 1,
        'num_items' => null,
        'num_pages' => null
    );

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

        // set URL and limit
        $this->pagination['url'] = FrontendNavigation::getURLForBlock('Blog');
        $this->pagination['limit'] = $this->get('fork.settings')->get('Blog', 'overview_num_items', 10);

        // populate count fields in pagination
        $this->pagination['num_items'] = FrontendBlogModel::getAllCount();
        $this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

        // num pages is always equal to at least 1
        if ($this->pagination['num_pages'] == 0) {
            $this->pagination['num_pages'] = 1;
        }

        // redirect if the request page doesn't exist
        if ($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

        // get articles
        $this->items = FrontendBlogModel::getAll($this->pagination['limit'], $this->pagination['offset']);
    }

    /**
     * Parse the data into the template
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
                 'title' => $this->get('fork.settings')->get('Blog', 'rss_title_' . FRONTEND_LANGUAGE),
                 'href' => $rssLink
            ),
            true
        );

        // assign articles
        $this->tpl->assign('items', $this->items);

        // parse the pagination
        $this->parsePagination();
    }
}
