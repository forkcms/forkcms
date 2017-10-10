<?php

namespace Backend\Modules\Faq\Widgets;

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * This widget will show the latest feedback
 */
class Feedback extends BackendBaseWidget
{
    /**
     * The feedback
     *
     * @var array
     */
    private $feedback = [];

    public function execute(): void
    {
        $this->setColumn('middle');
        $this->setPosition(0);
        $this->loadData();
        $this->parse();
        $this->display();
    }

    private function loadData(): void
    {
        $allFeedback = BackendFaqModel::getAllFeedback();

        // build the urls
        foreach ($allFeedback as $feedback) {
            $feedback['full_url'] = BackendModel::createUrlForAction('Edit', 'Faq') .
                                    '&id=' . $feedback['question_id'] . '#tabFeedback';
            $this->feedback[] = $feedback;
        }
    }

    private function parse(): void
    {
        $this->template->assign('faqFeedback', $this->feedback);
    }
}
