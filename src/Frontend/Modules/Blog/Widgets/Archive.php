<?php

namespace App\Frontend\Modules\Blog\Widgets;

use App\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use App\Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with the link to the archive
 */
class Archive extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        $this->template->assign('widgetBlogArchive', FrontendBlogModel::getArchiveNumbers());
    }
}
