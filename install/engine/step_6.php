<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Step 6 of the Fork installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class InstallerStep6 extends InstallerStep
{
	/**
	 * Executes this step.
	 */
	public function execute()
	{
		$this->loadForm();
		$this->validateForm();
		$this->parseForm();
	}

	/**
	 * Is this step allowed.
	 *
	 * @return bool
	 */
	public static function isAllowed()
	{
		return InstallerStep5::isAllowed() && isset($_SESSION['db_hostname']) && isset($_SESSION['db_database']) && isset($_SESSION['db_username']) && isset($_SESSION['db_password']);
	}

	/**
	 * Loads the form.
	 */
	private function loadForm()
	{
		// guess email
		$host = $_SERVER['HTTP_HOST'];

		$this->frm->addText('email', (SpoonSession::exists('email') ? SpoonSession::get('email') : 'info@' . $host));
		$this->frm->addPassword('password', (SpoonSession::exists('password') ? SpoonSession::get('password') : null), null, 'inputPassword', 'inputPasswordError', true);
		$this->frm->addPassword('confirm', (SpoonSession::exists('confirm') ? SpoonSession::get('confirm') : null), null, 'inputPassword', 'inputPasswordError', true);

		// disable autocomplete
		$this->frm->getField('password')->setAttributes(array('autocomplete' => 'off'));
		$this->frm->getField('confirm')->setAttributes(array('autocomplete' => 'off'));
	}

	/**
	 * Validate the form based on the variables in $_POST
	 */
	private function validateForm()
	{
		// form submitted
		if($this->frm->isSubmitted())
		{
			// required fields
			$this->frm->getField('email')->isEmail('Please provide a valid e-mailaddress.');
			$this->frm->getField('password')->isFilled('This field is required.');
			$this->frm->getField('confirm')->isFilled('This field is required.');
			if($this->frm->getField('password')->getValue() != $this->frm->getField('confirm')->getValue()) $this->frm->getField('confirm')->addError('The passwords do not match.');

			// all valid
			if($this->frm->isCorrect())
			{
				// update session
				SpoonSession::set('email', $this->frm->getField('email')->getValue());
				SpoonSession::set('password', $this->frm->getField('password')->getValue());
				SpoonSession::set('confirm', $this->frm->getField('confirm')->getValue());

				// redirect
				SpoonHTTP::redirect('index.php?step=7');
			}
		}
	}
}
