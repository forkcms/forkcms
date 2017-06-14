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
use Backend\Modules\Faq\Form\FaqFeedbackDeleteType;

/**
 * This is the DeleteFeedback action, it will display a form to create a new item
 */
class DeleteFeedback extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(FaqFeedbackDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        $feedbackId = $deleteFormData['id'];
        $feedback = BackendFaqModel::getFeedback($feedbackId);

        // there is no feedback data, so redirect
        if (empty($feedback)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        BackendFaqModel::deleteFeedback($feedbackId);
        $this->redirect(
            BackendModel::createURLForAction('Edit') . '&id=' .
            $feedback['question_id'] . '&report=deleted#tabFeedback'
        );
    }
}
