<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the DeleteFeedback action, it will display a form to create a new item
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendFaqDeleteFeedback extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$feedbackId = $this->getParameter('id', 'int');
		$feedback = BackendFaqModel::getFeedback($feedbackId);

		BackendModel::triggerEvent($this->getModule(), 'after_delete_feedback', array('item' => $feedback));

		// there is no feedback data, so redirect
		if(empty($feedback)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		BackendFaqModel::deleteFeedback($feedbackId);
		$this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $feedback['question_id'] . '&report=deleted#tabFeedback');
	}
}
