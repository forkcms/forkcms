<?php

namespace Frontend\Modules\ContentBlocks\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\ContentBlocks\Engine\Model as FrontendContentBlocksModel;

/**
 * This is the detail widget.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Jelmer Prins <jelmer@sumocoders.be>
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
