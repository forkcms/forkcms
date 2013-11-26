<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget which shows the subpages.
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class FrontendPagesWidgetSubpages extends FrontendBaseWidget
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

        $widgetTemplatesPath = FRONTEND_MODULES_PATH . '/pages/layout/widgets';

        // check if the given template exists
        try {
            $template = FrontendTheme::getPath($widgetTemplatesPath . '/' . $this->data['template']);
        } catch (FrontendException $e) {
            // template does not exist; assume subpages_default.tpl
            $template = FrontendTheme::getPath($widgetTemplatesPath . '/subpages_default.tpl');
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
