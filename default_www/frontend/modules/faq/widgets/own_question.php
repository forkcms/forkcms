<?php

/**
 * This is a widget with the form to ask a question
 *
 * @package		frontend
 * @subpackage	faq
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class FrontendFaqWidgetOwnQuestion extends FrontendBaseWidget
{
	/**
	 * Form instance
	 *
	 * @var FrontendForm
	 */
	private $frm;


	/**
	 * The form status
	 *
	 * @var string
	 */
	private $status = null;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// only show on the default action and if allowed
		if(!strpos(FrontendNavigation::getURLForBlock('faq'), $this->URL->getQueryString())) return;
		if(!FrontendModel::getModuleSetting('faq', 'allow_own_question', false)) return;

		// load form
		$this->loadForm();

		// validate form
		$this->validateForm();

		// parse
		$this->parse();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('own_question', '#' . FL::getAction('OwnQuestion'));

		// create elements
		$this->frm->addText('name')->setAttribute('placeholder', FL::getLabel('YourName'));
		$this->frm->addText('email')->setAttribute('placeholder', FL::getLabel('YourEmail'));
		$this->frm->addTextarea('message')->setAttribute('placeholder', FL::getLabel('YourQuestion'));
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the form or a status
		if(empty($this->status)) $this->frm->parse($this->tpl);
		else $this->tpl->assign($this->status, true);

		// parse an option so the stuff can be shown
		$this->tpl->assign('widgetFaqOwnQuestion', true);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate required fields
			$this->frm->getField('name')->isFilled(FL::err('NameIsRequired'));
			$this->frm->getField('email')->isEmail(FL::err('EmailIsInvalid'));
			$this->frm->getField('message')->isFilled(FL::err('QuestionIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// get module setting
				$spamFilterEnabled = FrontendModel::getModuleSetting('faq', 'spamfilter');

				// build variables
				$variables['sentOn'] = time();
				$variables['name'] = $this->frm->getField('name')->getValue();
				$variables['email'] = $this->frm->getField('email')->getValue();
				$variables['message'] = $this->frm->getField('message')->getValue();

				// should we check if the item is spam
				if($spamFilterEnabled)
				{
					// if the comment is spam alter the comment status so it will appear in the spam queue
					if(FrontendModel::isSpam($variables['message'], SITE_URL . $faqLink, $variables['name'], $variables['email']))
					{
						// save status
						$this->status = 'errorSpam';

						// stop here
						return;
					}
				}

				// save status
				$this->status = 'success';

				// track data
				$this->frm->trackData(array('form', 'own_question', 'form_token', '_utf8', 'message'));

				// add email
				FrontendMailer::addEmail(sprintf(FL::getMessage('FaqOwnQuestionSubject'), $variables['name']), FRONTEND_MODULES_PATH . '/faq/layout/templates/mails/own_question.tpl', $variables, $variables['email'], $variables['name']);
			}
		}
	}
}

?>