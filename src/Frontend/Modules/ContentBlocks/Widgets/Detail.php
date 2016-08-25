<?php

namespace Frontend\Modules\ContentBlocks\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\ContentBlocks\Engine\Model as FrontendContentBlocksModel;

/**
 * This is the detail widget.
 */
class Detail extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        $contentBlock = FrontendContentBlocksModel::get((int) $this->data['id']);

        // set a default text so we don't see the template data
        if (empty($contentBlock)) {
            $contentBlock['text'] = '';
        }

        $this->tpl->assign('widgetContentBlocks', $contentBlock);
        // That's all folks!
    }
}
