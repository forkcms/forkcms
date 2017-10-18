<?php

namespace Backend\Modules\ContentBlocks\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataGrid;

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
