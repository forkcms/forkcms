<?php

namespace App\Frontend\Modules\Faq\Widgets;

use App\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use App\Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;

/**
 * This is a widget with most read faq-questions
 */
class MostReadQuestions extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();

        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        $this->template->assign(
            'widgetFaqMostRead',
            FrontendFaqModel::getMostRead(
                $this->get('fork.settings')->get($this->getModule(), 'most_read_num_items', 10)
            )
        );
    }
}
