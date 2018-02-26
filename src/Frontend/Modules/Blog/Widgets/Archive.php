<?php

namespace ForkCMS\Frontend\Modules\Blog\Widgets;

use ForkCMS\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use ForkCMS\Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

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
