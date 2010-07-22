<?php

class InstallerStep4 extends InstallerStep
{
	/**
	 * Executes this step.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// load form
		$this->loadForm();

		// validate form
		$this->validateForm();

		// parse form
		$this->parseForm();

		// show output
		$this->tpl->display('layout/templates/4.tpl');
	}


	/**
	 * Is this step allowed.
	 *
	 * @return	bool
	 */
	public static function isAllowed()
	{
		return (isset($_SESSION['default_language']) && isset($_SESSION['multiple_languages']) && isset($_SESSION['languages']));
	}


	/**
	 * Loads the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		$this->frm->addText('email', (SpoonSession::exists('email') ? SpoonSession::get('email') : null));
		$this->frm->addPassword('password', (SpoonSession::exists('password') ? SpoonSession::get('password') : null), null, 'inputPassword', 'inputPasswordError', true);
	}

	/**
	 * Validate the form based on the variables in $_POST
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// form submitted
		if($this->frm->isSubmitted())
		{
			// required fields
			$this->frm->getField('email')->isEmail('This field is required.');
			$this->frm->getField('password')->isFilled('This field is required.');

			// all valid
			if($this->frm->isCorrect())
			{
				// update session
				SpoonSession::set('email', $this->frm->getField('email')->getValue());
				SpoonSession::set('password', $this->frm->getField('password')->getValue());

				// redirect
				SpoonHTTP::redirect('index.php?step=5');
			}
		}
	}
}

?>