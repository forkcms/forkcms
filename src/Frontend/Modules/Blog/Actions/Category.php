<?php

namespace Frontend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the category-action
 */
class Category extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var array
     */
    private $items;

    /**
     * The requested category
     *
     * @var array
     */
    private $category;

    /**
     * The pagination array
     * It will hold all needed parameters, some of them need initialization
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
        // get categories
        $categories = FrontendBlogModel::getAllCategories();
        $possibleCategories = [];
        foreach ($categories as $category) {
            $possibleCategories[$category['url']] = $category['id'];
        }

        // requested category
        $requestedCategory = \SpoonFilter::getValue(
            $this->url->getParameter(1, 'string'),
            array_keys($possibleCategories),
            'false'
        );

        // requested page
        $requestedPage = $this->url->getParameter('page', 'int', 1);

        // validate category
        if ($requestedCategory == 'false') {
            $this->redirect(FrontendNavigation::getUrl(404));
        }

        // set category
        $this->category = $categories[$possibleCategories[$requestedCategory]];

        // set URL and limit
        $this->pagination['url'] = FrontendNavigation::getUrlForBlock('Blog', 'Category') . '/' . $requestedCategory;
        $this->pagination['limit'] = $this->get('fork.settings')->get('Blog', 'overview_num_items', 10);

        // populate count fields in pagination
        $this->pagination['num_items'] = FrontendBlogModel::getAllForCategoryCount($requestedCategory);
        $this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

        // redirect if the request page doesn't exists
        if ($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) {
            $this->redirect(FrontendNavigation::getUrl(404));
        }

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

        // get articles
        $this->items = FrontendBlogModel::getAllForCategory(
            $requestedCategory,
            $this->pagination['limit'],
            $this->pagination['offset']
        );
    }

    private function parse(): void
    {
        // get RSS-link
        $rssTitle = $this->get('fork.settings')->get('Blog', 'rss_title_' . LANGUAGE);
        $rssLink = FrontendNavigation::getUrlForBlock('Blog', 'Rss');

        // add RSS-feed
        $this->header->addRssLink($rssTitle, $rssLink);

        // add into breadcrumb
        $this->breadcrumb->addElement(\SpoonFilter::ucfirst(FL::lbl('Category')));
        $this->breadcrumb->addElement($this->category['label']);

        // set pageTitle
        $this->header->setPageTitle(\SpoonFilter::ucfirst(FL::lbl('Category')));
        $this->header->setPageTitle($this->category['label']);

        // advanced SEO-attributes
        if (isset($this->category['meta_seo_index'])) {
            $this->header->addMetaData(
                ['name' => 'robots', 'content' => $this->category['meta_seo_index']]
            );
        }
        if (isset($this->category['meta_seo_follow'])) {
            $this->header->addMetaData(
                ['name' => 'robots', 'content' => $this->category['meta_seo_follow']]
            );
        }

        // assign category
        $this->template->assign('category', $this->category);

        // assign articles
        $this->template->assign('items', $this->items);

        // parse the pagination
        $this->parsePagination();
    }
}
