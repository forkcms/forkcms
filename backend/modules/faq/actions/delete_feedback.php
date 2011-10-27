<?php

/**
 * This is the ProcessFeedback action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.10
 */
class BackendFaqDeleteFeedback extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// the feedback id
		$feedbackId = $this->getParameter('id', 'int');

		// get the feedback
		$feedback = BackendFaqModel::getFeedback($feedbackId);

		// no feedback data
		if(empty($feedback)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		// process the feedback
		BackendFaqModel::deleteFeedback($feedbackId);

		// redirect
		$this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $feedback['question_id'] . '&report=deleted#tabFeedback');
	}
}
