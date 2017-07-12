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
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the overview-action
 */
class Index extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var array
     */
    private $items;

    /**
     * The pagination array
     * It will hold all needed parameters, some of them need initialization.
     *
     * @var array
     */
    protected $pagination = [
        'limit' => 10,
        'offset' => 0,
        'requested_page' => 1,
        'num_items' => null,
        'num_pages' => null,
    ];

    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    private function getData(): void
    {
        // requested page
        $requestedPage = $this->url->getParameter('page', 'int', 1);

        // set URL and limit
        $this->pagination['url'] = FrontendNavigation::getUrlForBlock('Blog');
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
            $this->redirect(FrontendNavigation::getUrl(404));
        }

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

        // get articles
        $this->items = FrontendBlogModel::getAll($this->pagination['limit'], $this->pagination['offset']);
    }

    private function parse(): void
    {
        // get RSS-link
        $rssLink = FrontendNavigation::getUrlForBlock('Blog', 'Rss');

        // add RSS-feed
        $this->header->addLink(
            [
                 'rel' => 'alternate',
                 'type' => 'application/rss+xml',
                 'title' => $this->get('fork.settings')->get('Blog', 'rss_title_' . LANGUAGE),
                 'href' => $rssLink,
            ],
            true
        );

        // assign articles
        $this->template->assign('items', $this->items);

        // parse the pagination
        $this->parsePagination();
    }
}
