<?php

namespace Frontend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;

/**
 * This is the index-action
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Index extends FrontendBaseBlock
{
    /**
     * @var    array
     */
    private $items = array();

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        $this->getData();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        $categories = FrontendFaqModel::getCategories();
        $limit = FrontendModel::getModuleSetting('Faq', 'overview_num_items_per_category', 10);

        foreach ($categories as $item) {
            $item['questions'] = FrontendFaqModel::getAllForCategory($item['id'], $limit);

            // no questions? next!
            if (empty($item['questions'])) {
                continue;
            }

            // add the category item including the questions
            $this->items[] = $item;
        }
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        $this->tpl->assign('faqCategories', (array) $this->items);
        $this->tpl->assign(
            'allowMultipleCategories',
            FrontendModel::getModuleSetting('Faq', 'allow_multiple_categories', true)
        );
    }
}
