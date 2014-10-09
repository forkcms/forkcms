<?php

namespace Frontend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the category-action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Category extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var    array
     */
    private $items;

    /**
     * The requested category
     *
     * @var    array
     */
    private $category;

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
        // get categories
        $categories = FrontendBlogModel::getAllCategories();
        $possibleCategories = array();
        foreach ($categories as $category) {
            $possibleCategories[$category['url']] = $category['id'];
        }

        // requested category
        $requestedCategory = \SpoonFilter::getValue(
            $this->URL->getParameter(1, 'string'),
            array_keys($possibleCategories),
            'false'
        );

        // validate category
        if ($requestedCategory == 'false') {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // set category
        $this->category = $categories[$possibleCategories[$requestedCategory]];

        // requested page
        $requestedPage = $this->URL->getParameter('page', 'int', 1);

        $currentCategoryUrl = FrontendNavigation::getURLForBlock('Blog', 'Category') . '/' . $requestedCategory;

        // get articles
        $this->items = $this->parsePagination(
            function() use ($requestedCategory) {
                return FrontendBlogModel::getAllForCategoryCount($requestedCategory);
            },
            function($offset, $length) use ($requestedCategory) {
                return FrontendBlogModel::getAllForCategory($requestedCategory, $length, $offset);
            },
            function($page) use ($currentCategoryUrl) {
                return $currentCategoryUrl . '?page=' . $page;
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

        // add into breadcrumb
        $this->breadcrumb->addElement(\SpoonFilter::ucfirst(FL::lbl('Category')));
        $this->breadcrumb->addElement($this->category['label']);

        // set pageTitle
        $this->header->setPageTitle(\SpoonFilter::ucfirst(FL::lbl('Category')));
        $this->header->setPageTitle($this->category['label']);

        // advanced SEO-attributes
        if (isset($this->category['meta_data']['seo_index'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->category['meta_data']['seo_index'])
            );
        }
        if (isset($this->category['meta_data']['seo_follow'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->category['meta_data']['seo_follow'])
            );
        }

        // assign category
        $this->tpl->assign('category', $this->category);

        // assign articles
        $this->tpl->assign('items', $this->items);
    }
}
