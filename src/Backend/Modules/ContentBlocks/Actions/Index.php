<?php

namespace Backend\Modules\ContentBlocks\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use App\Component\Locale\BackendLocale;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataGrid;

/**
 * This is the index-action (default), it will display the overview
 */
class Index extends BackendBaseActionIndex
{
    public function execute(): void
    {
        parent::execute();
        $this->template->assign('dataGrid', ContentBlockDataGrid::getHtml(BackendLocale::workingLocale()));
        $this->parse();
        $this->display();
    }
}
