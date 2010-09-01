<?php

/**
 * InstallerStep4
 * Step 4 of the Fork installer
 *
 * @package		installer
 * @subpackage	install
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author 		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
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
		$this->validate();

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
		$this->frm->addPassword('confirm', (SpoonSession::exists('confirm') ? SpoonSession::get('confirm') : null), null, 'inputPassword', 'inputPasswordError', true);

		// disable autocomplete
		$this->frm->getField('password')->setAttributes(array('autocomplete' => 'off'));
		$this->frm->getField('confirm')->setAttributes(array('autocomplete' => 'off'));
	}

	/**
	 * Validate the form based on the variables in $_POST
	 *
	 * @return	void
	 */
	private function validate()
	{
		// form submitted
		if($this->frm->isSubmitted())
		{
			// required fields
			$this->frm->getField('email')->isEmail('This field is required.');
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
				SpoonHTTP::redirect('index.php?step=5');
			}
		}
	}
}

?>