<?php

namespace Backend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * This is the DeleteFeedback action, it will display a form to create a new item
 */
class DeleteFeedback extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $feedbackId = $this->getParameter('id', 'int');
        $feedback = BackendFaqModel::getFeedback($feedbackId);

        // there is no feedback data, so redirect
        if (empty($feedback)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        BackendFaqModel::deleteFeedback($feedbackId);
        $this->redirect(
            BackendModel::createURLForAction('Edit') . '&amp;id=' .
            $feedback['question_id'] . '&report=deleted#tabFeedback'
        );
    }
}
