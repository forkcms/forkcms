<?php

/**
 * Step 3 of the Fork installer
 *
 * @package		install
 * @subpackage	installer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class InstallerStep3 extends InstallerStep
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
		$this->tpl->display('layout/templates/3.tpl');
	}


	/**
	 * Is this step allowed.
	 *
	 * @return	bool
	 */
	public static function isAllowed()
	{
		return InstallerStep2::checkRequirements();
	}


	/**
	 * Loads the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// seperate frontend/backend languages?
		$this->frm->addCheckbox('same_interface_language', (SpoonSession::exists('same_interface_language') ? SpoonSession::get('same_interface_language') : true));

		// multiple or single language (frontend)
		$this->frm->addRadiobutton('language_type',	array(array('value' => 'multiple', 'label' => 'Multiple languages', 'variables' => array('multiple' => true)),
													array('value' => 'single', 'label' => 'Just one language', 'variables' => array('single' => true))), (SpoonSession::exists('multiple_languages') && SpoonSession::get('multiple_languages')) ? 'multiple' : 'single');

		// multiple languages (frontend)
		$this->frm->addMultiCheckbox('languages', array(array('value' => 'en', 'label' => 'English'),
														array('value' => 'fr', 'label' => 'French'),
														array('value' => 'nl', 'label' => 'Dutch')), (SpoonSession::exists('languages') ? SpoonSession::get('languages') : 'en'));

		// multiple languages (backend)
		$this->frm->addMultiCheckbox('interface_languages', array(array('value' => 'en', 'label' => 'English'),
																	array('value' => 'fr', 'label' => 'French'),
																	array('value' => 'nl', 'label' => 'Dutch')), (SpoonSession::exists('interface_languages') ? SpoonSession::get('interface_languages') : 'en'));

		// single language (frontend)
		$this->frm->addDropdown('language', array('en' => 'English', 'fr' => 'French', 'nl' => 'Dutch'), (SpoonSession::exists('default_language') ? SpoonSession::get('default_language') : 'en'));

		// default language (frontend)
		$this->frm->addDropdown('default_language', array('en' => 'English', 'fr' => 'French', 'nl' => 'Dutch'), (SpoonSession::exists('default_language') ? SpoonSession::get('default_language') : 'en'));

		// default language (backend)
		$this->frm->addDropdown('default_interface_language', array('en' => 'English', 'fr' => 'French', 'nl' => 'Dutch'), (SpoonSession::exists('default_interface_language') ? SpoonSession::get('default_interface_language') : 'en'));
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
			// multiple languages
			if($this->frm->getField('language_type')->getValue() == 'multiple')
			{
				// list of languages
				$languages = $this->frm->getField('languages')->getValue();

				// default language
				if(!in_array($this->frm->getField('default_language')->getValue(), $languages)) $this->frm->getField('default_language')->setError('Your default language needs to be in the list of languages you chose.');
			}

			// single language
			else
			{
				// list of languages
				$languages = (array) array($this->frm->getField('default_language')->getValue());
			}

			// same cms interface language
			if($this->frm->getField('same_interface_language')->getChecked())
			{
				// list of languages
				$interfaceLanguages = $languages;
			}

			// different interface language
			else
			{
				// list of languages
				$interfaceLanguages = $this->frm->getField('interface_languages')->getValue();
			}

			// default language
			if(!in_array($this->frm->getField('default_interface_language')->getValue(), $interfaceLanguages)) $this->frm->getField('default_interface_language')->setError('Your default language needs to be in the list of languages you chose.');

			// all valid
			if($this->frm->isCorrect())
			{
				// set languages
				SpoonSession::set('default_language', $this->frm->getField('default_language')->getValue());
				SpoonSession::set('default_interface_language', $this->frm->getField('default_interface_language')->getValue());
				SpoonSession::set('multiple_languages', ($this->frm->getField('language_type')->getValue() == 'multiple') ? true : false);
				SpoonSession::set('languages', $languages);
				SpoonSession::set('interface_languages', $interfaceLanguages);

				// redirect
				SpoonHTTP::redirect('index.php?step=4');
			}
		}
	}
}

?>