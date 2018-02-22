<?php

namespace App\Frontend\Modules\Blog\Widgets;

use App\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use App\Frontend\Core\Engine\Navigation as FrontendNavigation;
use App\Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with the blog-categories
 */
class Categories extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        // get categories
        $categories = FrontendBlogModel::getAllCategories();

        // any categories?
        if (!empty($categories)) {
            // build link
            $link = FrontendNavigation::getUrlForBlock('Blog', 'Category');

            // loop and reset url
            foreach ($categories as &$row) {
                $row['url'] = $link . '/' . $row['url'];
            }
        }

        // assign comments
        $this->template->assign('widgetBlogCategories', $categories);
    }
}
