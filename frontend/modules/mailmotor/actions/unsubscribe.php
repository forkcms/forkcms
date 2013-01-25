<?php

/**
 * This is the index-action
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class FrontendMailmotorUnsubscribe extends FrontendBaseBlock
{
	/**
	 * The email address passed to this page
	 *
	 * @var	string
	 */
	private $email;

	/**
	 * FrontendForm instance
	 *
	 * @var	FrontendForm
	 */
	private $frm;

	/**
	 * The group passed to this page
	 *
	 * @var	int
	 */
	private $group;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// check if an email was given
		$this->group = SpoonFilter::getGetValue('group', null, '');
		$this->email = urldecode(SpoonFilter::getGetValue('email', null, ''));

		$this->loadTemplate();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create the form
		$this->frm = new FrontendForm('unsubscribe', null, null, 'unsubscribeForm');

		// create & add elements
		$this->frm->addText('email')->setAttributes(array('required' => null, 'type' => 'email'));
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// form was sent?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show message
			$this->tpl->assign('unsubscribeIsSuccess', true);

			// hide form
			$this->tpl->assign('unsubscribeHideForm', true);
		}

		// unsubscribe was issued for a specific group/address
		if(SpoonFilter::isEmail($this->email) && FrontendMailmotorModel::existsGroup($this->group))
		{
			// unsubscribe the address from this group
			if(FrontendMailmotorModel::unsubscribe($this->email, $this->group))
			{
				// hide form
				$this->tpl->assign('unsubscribeHideForm', true);

				// show message
				$this->tpl->assign('unsubscribeIsSuccess', true);
			}

			// unsubscribe failed, show an error
			else
			{
				// show message
				$this->tpl->assign('unsubscribeHasError', true);
			}
		}

		// parse the form
		$this->frm->parse($this->tpl);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// get values
			$email = $this->frm->getField('email');

			// validate required fields
			if($email->isEmail(FL::err('EmailIsInvalid')))
			{
				// email does not exist
				if(!FrontendMailmotorModel::exists($email->getValue())) $email->addError(FL::err('EmailNotInDatabase'));

				// user is already unsubscribed
				if(!FrontendMailmotorModel::isSubscribed($email->getValue(), $this->group)) $email->addError(FL::err('AlreadyUnsubscribed'));
			}

			// no errors and email address does not exist
			if($this->frm->isCorrect())
			{
				try
				{
					// unsubscribe the user from our default group
					if(!FrontendMailmotorCMHelper::unsubscribe($email->getValue(), $this->group)) throw new FrontendException('Could not unsubscribe');

					// trigger event
					FrontendModel::triggerEvent('mailmotor', 'after_unsubscribe', array('email' => $email->getValue()));

					// redirect
					$this->redirect(FrontendNavigation::getURLForBlock('mailmotor', 'unsubscribe') . '?sent=true#unsubscribeForm');
				}
				catch(Exception $e)
				{
					// when debugging we need to see the exceptions
					if(SPOON_DEBUG) throw $e;

					// show error
					$this->tpl->assign('unsubscribeHasError', true);
				}
			}

			// show errors
			else $this->tpl->assign('unsubscribeHasFormError', true);
		}
	}
}
