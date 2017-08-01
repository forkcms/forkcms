<?php

namespace Frontend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;

/**
 * This is the index-action
 */
class Index extends FrontendBaseBlock
{
    /**
     * @var array
     */
    private $items = [];

    public function execute(): void
    {
        parent::execute();

        $this->getData();
        $this->loadTemplate();
        $this->parse();
    }

    private function getData(): void
    {
        $categories = FrontendFaqModel::getCategories();
        $limit = $this->get('fork.settings')->get('Faq', 'overview_num_items_per_category', 10);

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

    private function parse(): void
    {
        $this->template->assign('faqCategories', (array) $this->items);
        $this->template->assign(
            'allowMultipleCategories',
            $this->get('fork.settings')->get('Faq', 'allow_multiple_categories', true)
        );
    }
}
