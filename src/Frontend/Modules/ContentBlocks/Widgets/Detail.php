<?php

namespace Frontend\Modules\ContentBlocks\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Theme as FrontendTheme;
use Frontend\Modules\ContentBlocks\Engine\Model as FrontendContentBlocksModel;

/**
 * This is the detail widget.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Detail extends FrontendBaseWidget
{
    /**
     * The item.
     *
     * @var    array
     */
    private $item;

    /**
     * Assign the template path
     *
     * @return string
     */
    private function assignTemplate()
    {
        $template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/ContentBlocks/Layout/Widgets/Default.tpl');

        // is the content block visible?
        if (!empty($this->item)) {
            // check if the given template exists
            try {
                $template = FrontendTheme::getPath(
                    FRONTEND_MODULES_PATH . '/ContentBlocks/Layout/Widgets/' . $this->item['template']
                );
            } catch (FrontendException $e) {
                // do nothing
            }
        } else {
            // set a default text so we don't see the template data
            $this->item['text'] = '';
        }

        return $template;
    }

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadData();
        $template = $this->assignTemplate();
        $this->loadTemplate($template);
        $this->parse();
    }

    /**
     * Load the data
     */
    private function loadData()
    {
        $this->item = FrontendContentBlocksModel::get((int) $this->data['id']);
    }

    /**
     * Parse into template
     */
    private function parse()
    {
        // assign data
        $this->tpl->assign('widgetContentBlocks', $this->item);
    }
}
