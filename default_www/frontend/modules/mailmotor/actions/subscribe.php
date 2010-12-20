<?php

/**
 * FrontendMailmotorSubscribe
 * This is the subscribe-action
 *
 * @package		frontend
 * @subpackage	mailmotor
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class FrontendMailmotorSubscribe extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance
	 *
	 * @var	FrontendForm
	 */
	private $frm;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// load template
		$this->loadTemplate();

		// load
		$this->loadForm();

		// validate
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
		// create the form
		$this->frm = new FrontendForm('subscribe', null, null, 'subscribeForm');

		// create & add elements
		$this->frm->addText('email');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// form was sent?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show message
			$this->tpl->assign('subscribeIsSuccess', true);

			// hide form
			$this->tpl->assign('subscribeHideForm', true);
		}

		// parse the form
		$this->frm->parse($this->tpl);
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
			// validate required fields
			$email = $this->frm->getField('email');

			// validate required fields
			if($email->isEmail(FL::getError('EmailIsInvalid')))
			{
				if(FrontendMailmotorModel::isSubscribed($email->getValue())) $email->addError(FL::getError('AlreadySubscribed'));
			}

			// no errors
			if($this->frm->isCorrect())
			{
				try
				{
					// subscribe the user to our default group
					FrontendMailmotorCMHelper::subscribe($email->getValue());

					// redirect
					$this->redirect(FrontendNavigation::getURLForBlock('mailmotor', 'subscribe') .'?sent=true#subscribeForm');
				}
				catch(Exception $e)
				{
					// when debugging we need to see the exceptions
					if(SPOON_DEBUG) throw $e;

					// show error
					$this->tpl->assign('subscribeHasError', true);
				}
			}

			// show errors
			else $this->tpl->assign('subscribeHasFormError', true);
		}
	}

}

?>