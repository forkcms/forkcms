<?php

/**
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	faq
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.3
 */
class FrontendFaqDetail extends FrontendBaseBlock
{
	/**
	 * Form instance
	 *
	 * @var FrontendForm
	 */
	private $frm;


	/**
	 * The faq
	 *
	 * @var	array
	 */
	private $record;


	/**
	 * The settings
	 *
	 * @var	array
	 */
	private $settings;


	/**
	 * The status of the form
	 *
	 * @var	string
	 */
	private $status = null;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// hide contenTitle, in the template the title is wrapped with an inverse-option
		$this->tpl->assign('hideContentTitle', true);

		// load template
		$this->loadTemplate();

		// load the data
		$this->getData();

		// update stats
		$this->updateStatistics();

		// load form
		$this->loadForm();

		// validate form
		$this->validateForm();

		// parse
		$this->parse();
	}


	/**
	 * Load the data, don't forget to validate the incoming data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// get by URL
		$this->record = FrontendFaqModel::get($this->URL->getParameter(1));

		// anything found?
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// overwrite URLs
		$this->record['category_full_url'] = FrontendNavigation::getURLForBlock('faq', 'category') . '/' . $this->record['category_url'];
		$this->record['full_url'] = FrontendNavigation::getURLForBlock('faq', 'detail') . '/' . $this->record['url'];

		// get tags
		$this->record['tags'] = FrontendTagsModel::getForItem('faq', $this->record['id']);

		// get settings
		$this->settings = FrontendModel::getModuleSettings('faq');

		// reset allow comments
		if(!$this->settings['allow_feedback']) $this->record['allow_feedback'] = false;

		// ge status
		$this->status = $this->URL->getParameter(2);
		if($this->status == FL::getAction('Success')) $this->status = 'success';
		if($this->status == FL::getAction('Spam')) $this->status = 'spam';
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('feedback');

		// set hidden values
		$rbtUsefullValues[] = array('label' => FL::lbl('Yes'), 'value' => 'Y');
		$rbtUsefullValues[] = array('label' => FL::lbl('No'), 'value' => 'N');

		// create elements
		$this->frm->addHidden('question_id', $this->record['id']);
		$this->frm->addRadiobutton('usefull', $rbtUsefullValues);
		$this->frm->addTextarea('message');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['category_title'], $this->record['category_full_url']);
		$this->breadcrumb->addElement($this->record['question']);

		// set meta
		$this->header->setPageTitle($this->record['category_title']);
		$this->header->setPageTitle($this->record['question']);

		// assign article
		$this->tpl->assign('item', $this->record);

		// assign items in the same category and related items
		$this->tpl->assign('inSameCategory', FrontendFaqModel::getAllForCategory($this->record['category_id'], $this->settings['related_num_items'], $this->record['id']));
		$this->tpl->assign('related', FrontendFaqModel::getRelated($this->record['id'], $this->settings['related_num_items']));

		// assign settings
		$this->tpl->assign('settings', $this->settings);

		// parse the form
		if(empty($this->status)) $this->frm->parse($this->tpl);

		// parse the form status
		if(!empty($this->status)) $this->tpl->assign($this->status, true);
	}


	/**
	 * Update the view count for this item
	 *
	 * @return	void
	 */
	private function updateStatistics()
	{
		// view has been counted
		if(SpoonSession::exists('viewed_faq_' . $this->record['id'])) return;

		// update view count
		FrontendFaqModel::increaseViewCount($this->record['id']);

		// save in session so we know this view has been counted
		SpoonSession::set('viewed_faq_' . $this->record['id'], true);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// get settings
		$feedbackAllowed = (isset($this->settings['allow_feedback']) && $this->settings['allow_feedback']);

		// feedback isn't allowed so we don't have to validate
		if(!$feedbackAllowed) return false;

		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// reformat data
			$usefull = ($this->frm->getField('usefull')->getValue() == 'Y');

			// the form has been sent
			$this->tpl->assign('hideFeedbackNoInfo', $usefull);

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate required fields
			$this->frm->getField('message')->isFilled(FL::err('FeedbackIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// reformat data
				$text = $this->frm->getField('message')->getValue();

				// get feedback in session
				$previousFeedback = (SpoonSession::exists('faq_feedback_' . $this->record['id']) ? SpoonSession::get('faq_feedback_' . $this->record['id']) : null);

				// update counters
				FrontendFaqModel::updateFeedback($this->record['id'], $usefull, $previousFeedback);

				// save feedback in session
				SpoonSession::set('faq_feedback_' . $this->record['id'], $usefull);

				// answer is yes so there's no feedback
				if($usefull == 'Y') $this->redirect($this->record['full_url'] . '/' . FL::getAction('Success'));

				// answer is no so send the feedback
				else
				{
					// get module setting
					$spamFilterEnabled = (isset($this->settings['spamfilter']) && $this->settings['spamfilter']);

					// build array
					$variables['question'] = $this->record['question'];
					$variables['sentOn'] = time();
					$variables['text'] = $text;

					// should we check if the item is spam
					if($spamFilterEnabled)
					{
						// the comment is spam
						if(FrontendModel::isSpam($text, $variables['question_link']))
						{
							// set the status to spam
							$this->redirect($this->record['full_url'] . '/' . FL::getAction('Spam'));

							// return
							return;
						}
					}

					// add email
					FrontendMailer::addEmail(sprintf(FL::getMessage('FaqFeedbackSubject'), $this->record['question']), FRONTEND_MODULES_PATH . '/faq/layout/templates/mails/feedback.tpl', $variables);

					// save status
					$this->redirect($this->record['full_url'] . '/' . FL::getAction('Success'));
				}
			}
		}

		// form hasn't been sent
		else $this->tpl->assign('hideFeedbackNoInfo', true);
	}
}

?>