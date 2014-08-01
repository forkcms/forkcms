<?php

namespace Backend\Modules\Faq\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * This widget will show the latest feedback
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Feedback extends BackendBaseWidget
{
    /**
     * The feedback
     *
     * @var	array
     */
    private $feedback = array();

    /**
     * Execute the widget
     */
    public function execute()
    {
        $this->setColumn('middle');
        $this->setPosition(0);
        $this->loadData();
        $this->parse();
        $this->display();
    }

    /**
     * Load the data
     */
    private function loadData()
    {
        $allFeedback = BackendFaqModel::getAllFeedback();

        // build the urls
        foreach ($allFeedback as $feedback) {
            $feedback['full_url'] = BackendModel::createURLForAction('Edit', 'Faq') .
                                    '&id=' . $feedback['question_id'] . '#tabFeedback';
            $this->feedback[] = $feedback;
        }
    }

    /**
     * Parse into template
     */
    private function parse()
    {
        $this->tpl->assign('faqFeedback', $this->feedback);
    }
}
