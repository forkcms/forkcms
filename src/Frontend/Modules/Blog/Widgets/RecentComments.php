<?php

namespace App\Frontend\Modules\Blog\Widgets;

use App\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use App\Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with recent comments on all blog-articles
 */
class RecentComments extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        $this->template->assign('widgetBlogRecentComments', FrontendBlogModel::getRecentComments(5));
    }
}
