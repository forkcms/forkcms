<?php

namespace Frontend\Modules\Faq\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;

/**
 * This is a widget with most read faq-questions
 *
 * @author Jonas De Keukelaere <jonas@sumocoders.be>
 */
class CategoryList extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        // call parent
        parent::execute();

        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        $this->tpl->assign('widgetFaqCategory', FrontendFaqModel::getCategoryById($this->data['id']));
        $this->tpl->assign(
            'widgetFaqCategoryList',
            FrontendFaqModel::getAllForCategory(
                $this->data['id'],
                FrontendModel::getModuleSetting('Faq', 'most_read_num_items', 10)
            )
        );
    }
}
