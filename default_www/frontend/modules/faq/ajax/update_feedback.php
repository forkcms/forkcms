<?php

/**
 * Update the feedback counter
 *
 * @package		frontend
 * @subpackage	faq
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.1
 */
class FrontendFaqAjaxUpdateFeedback extends FrontendBaseAJAXAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$questionId = SpoonFilter::getPostValue('question_id', null, 0, 'int');
		$usefull = SpoonFilter::getPostValue('usefull', null, '', 'string');

		// validate
		if($questionId === 0) $this->output(self::ERROR, null, 'invalid question_id');
		if($usefull === '') $this->output(self::ERROR, null, 'invalid feedback');

		// redefine vars
		$usefull = ($usefull == 'Y');

		// get feedback in session
		$previousFeedback = (SpoonSession::exists('faq_feedback_' . $questionId) ? SpoonSession::get('faq_feedback_' . $questionId) : null);

		// update counters
		FrontendFaqModel::updateFeedback($questionId, $usefull, $previousFeedback);

		// save feedback in session
		SpoonSession::set('faq_feedback_' . $questionId, $usefull);

		// success output
		$this->output(self::OK, null, 'feedback updated');
	}
}

?>
