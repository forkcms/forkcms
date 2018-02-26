<?php

namespace ForkCMS\Frontend\Modules\Blog\Widgets;

use ForkCMS\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use ForkCMS\Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

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
