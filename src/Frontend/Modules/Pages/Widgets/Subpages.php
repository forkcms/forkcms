<?php

namespace Frontend\Modules\Pages\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Theme as FrontendTheme;
use Frontend\Modules\Pages\Engine\Model as FrontendPagesModel;

/**
 * This is a widget which shows the subpages.
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class Subpages extends FrontendBaseWidget
{
    /**
     * The items.
     *
     * @var    array
     */
    private $items;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadData();

        $widgetTemplatesPath = FRONTEND_MODULES_PATH . '/Pages/Layout/Widgets';

        // check if the given template exists
        try {
            $template = FrontendTheme::getPath($widgetTemplatesPath . '/' . $this->data['template']);
        } catch (FrontendException $e) {
            // template does not exist; assume subpages_default.tpl
            $template = FrontendTheme::getPath($widgetTemplatesPath . '/SubpagesDefault.tpl');
        }

        $this->loadTemplate($template);
        $this->parse();
    }

    /**
     * Load the data
     */
    private function loadData()
    {
        // get the current page id
        $pageId = $this->getContainer()->get('page')->getId();

        // fetch the items
        $this->items = FrontendPagesModel::getSubpages($pageId);
    }

    /**
     * Parse
     */
    private function parse()
    {
        $this->tpl->assign('widgetSubpages', $this->items);
    }
}
