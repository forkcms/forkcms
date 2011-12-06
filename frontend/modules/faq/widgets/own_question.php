<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the form to ask a question
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
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
	 */
	public function execute()
	{
		parent::execute();

		$this->loadTemplate();

		if(!FrontendModel::getModuleSetting('faq', 'allow_own_question', false)) return;

		$this->loadForm();
		$this->validateForm();
		$this->parse();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('own_question', '#' . FL::getAction('OwnQuestion'));
		$this->frm->addText('name')->setAttribute('placeholder', FL::getLabel('YourName'));
		$this->frm->addText('email')->setAttribute('placeholder', FL::getLabel('YourEmail'));
		$this->frm->addTextarea('message')->setAttribute('placeholder', FL::getLabel('YourQuestion'));
	}

	/**
	 * Parse
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
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validate required fields
			$this->frm->getField('name')->isFilled(FL::err('NameIsRequired'));
			$this->frm->getField('email')->isEmail(FL::err('EmailIsInvalid'));
			$this->frm->getField('message')->isFilled(FL::err('QuestionIsRequired'));

			if($this->frm->isCorrect())
			{
				$spamFilterEnabled = FrontendModel::getModuleSetting('faq', 'spamfilter');
				$variables['sentOn'] = time();
				$variables['name'] = $this->frm->getField('name')->getValue();
				$variables['email'] = $this->frm->getField('email')->getValue();
				$variables['message'] = $this->frm->getField('message')->getValue();

				if($spamFilterEnabled)
				{
					// if the comment is spam alter the comment status so it will appear in the spam queue
					if(FrontendModel::isSpam($variables['message'], SITE_URL . FrontendNavigation::getURLForBlock('faq'), $variables['name'], $variables['email']))
					{
						$this->status = 'errorSpam';
						return;
					}
				}

				$this->status = 'success';
				FrontendMailer::addEmail(sprintf(FL::getMessage('FaqOwnQuestionSubject'), $variables['name']), FRONTEND_MODULES_PATH . '/faq/layout/templates/mails/own_question.tpl', $variables, $variables['email'], $variables['name']);
			}
		}
	}
}
