<?php

namespace Frontend\Modules\Blog\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Category extends FrontendBaseBlock
{
    /** @var array */
    private $articles;

    /** @var array */
    private $category;

    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    private function getCategory(): array
    {
        $category = FrontendBlogModel::getCategory($this->url->getParameter(1));

        if (empty($category)) {
            throw new NotFoundHttpException();
        }

        return $category;
    }

    private function buildUrl(): string
    {
        return FrontendNavigation::getUrlForBlock($this->getModule(), $this->getAction())
               . '/' . $this->category['url'];
    }

    private function buildPaginationConfig(): array
    {
        $requestedPage = $this->url->getParameter('page', 'int', 1);
        $numberOfItems = FrontendBlogModel::getAllForCategoryCount($this->category['url']);

        $limit = $this->get('fork.settings')->get($this->getModule(), 'overview_num_items', 10);
        $numberOfPages = (int) ceil($numberOfItems / $limit);

        if ($numberOfPages === 0) {
            $numberOfPages = 1;
        }

        // Check if the page exists
        if ($requestedPage > $numberOfPages || $requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        return [
            'url' => $this->buildUrl(),
            'limit' => $limit,
            'offset' => ($requestedPage * $limit) - $limit,
            'requested_page' => $requestedPage,
            'num_items' => $numberOfItems,
            'num_pages' => $numberOfPages,
        ];
    }

    private function getData(): void
    {
        $this->category = $this->getCategory();
        $this->pagination = $this->buildPaginationConfig();

        $this->articles = FrontendBlogModel::getAllForCategory(
            $this->category['url'],
            $this->pagination['limit'],
            $this->pagination['offset']
        );
    }

    private function addLinkToRssFeed(): void
    {
        $this->header->addRssLink(
            $this->get('fork.settings')->get($this->getModule(), 'rss_title_' . LANGUAGE, SITE_DEFAULT_TITLE),
            FrontendNavigation::getUrlForBlock($this->getModule(), 'Rss')
        );
    }

    private function addCategoryToBreadcrumb(): void
    {
        $this->breadcrumb->addElement(\SpoonFilter::ucfirst(FL::lbl('Category')));
        $this->breadcrumb->addElement($this->category['label']);
    }

    private function setPageTitle(): void
    {
        $this->header->setPageTitle(\SpoonFilter::ucfirst(FL::lbl('Category')));
        $this->header->setPageTitle($this->category['label']);
    }

    private function parse(): void
    {
        $this->addLinkToRssFeed();
        $this->addCategoryToBreadcrumb();
        $this->setPageTitle();
        $this->parsePagination();

        $this->template->assign('category', $this->category);
        $this->template->assign('items', $this->articles);
        $this->setMeta($this->category['meta']);
    }
}
