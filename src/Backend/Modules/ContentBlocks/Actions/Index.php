<?php

namespace ForkCMS\Backend\Modules\ContentBlocks\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use ForkCMS\Backend\Core\Language\Locale;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataGrid;

/**
 * This is the index-action (default), it will display the overview
 */
class Index extends BackendBaseActionIndex
{
    public function execute(): void
    {
        parent::execute();
        $this->template->assign('dataGrid', ContentBlockDataGrid::getHtml(Locale::workingLocale()));
        $this->parse();
        $this->display();
    }
}
