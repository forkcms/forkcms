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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Index extends FrontendBaseBlock
{
    /** @var array */
    private $articles;

    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    private function buildPaginationConfig(): array
    {
        $requestedPage = $this->url->getParameter('page', 'int', 1);
        $numberOfItems = FrontendBlogModel::getAllCount();

        $limit = $this->get('fork.settings')->get($this->getModule(), 'overview_num_items', 10);
        $numberOfPages = (int) ceil($numberOfItems / $limit);

        // Check if the page exists
        if ($requestedPage > $numberOfPages || $requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        return [
            'url' => FrontendNavigation::getUrlForBlock($this->getModule()),
            'limit' => $limit,
            'offset' => ($requestedPage * $limit) - $limit,
            'requested_page' => $requestedPage,
            'num_items' => $numberOfItems,
            'num_pages' => $numberOfPages,
        ];
    }

    private function getData(): void
    {
        $this->pagination = $this->buildPaginationConfig();
        $this->articles = FrontendBlogModel::getAll($this->pagination['limit'], $this->pagination['offset']);
    }

    private function addLinkToRssFeed(): void
    {
        $this->header->addRssLink(
            $this->get('fork.settings')->get($this->getModule(), 'rss_title_' . LANGUAGE),
            FrontendNavigation::getUrlForBlock($this->getModule(), 'Rss')
        );
    }

    private function parse(): void
    {
        $this->addLinkToRssFeed();
        $this->parsePagination();

        $this->template->assign('items', $this->articles);
    }
}
