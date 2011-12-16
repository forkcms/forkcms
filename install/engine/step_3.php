<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Step 3 of the Fork installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class InstallerStep3 extends InstallerStep
{
	/**
	 * Executes this step.
	 */
	public function execute()
	{
		$this->loadForm();
		$this->validateForm();
		$this->parseForm();
		$this->tpl->display('layout/templates/step_3.tpl');
	}

	/**
	 * Is this step allowed.
	 *
	 * @return bool
	 */
	public static function isAllowed()
	{
		return InstallerStep2::isAllowed() && InstallerStep2::checkRequirements();
	}

	/**
	 * Loads the form.
	 */
	private function loadForm()
	{
		// seperate frontend/backend languages?
		$this->frm->addCheckbox('same_interface_language', (SpoonSession::exists('same_interface_language') ? SpoonSession::get('same_interface_language') : true));

		// multiple or single language (frontend)
		$this->frm->addRadiobutton(
			'language_type',
			array(
				array(
					'value' => 'multiple',
					'label' => 'Multiple languages',
					'variables' => array('multiple' => true)
				),
				array(
					'value' => 'single',
					'label' => 'Just one language',
					'variables' => array('single' => true)
				)
			),
			(SpoonSession::exists('multiple_languages') && SpoonSession::get('multiple_languages')) ? 'multiple' : 'single'
		);

		// multiple languages (frontend)
		$this->frm->addMultiCheckbox('languages',
			array(
				array('value' => 'en', 'label' => 'English'),
				array('value' => 'cn', 'label' => 'Chinese'),
				array('value' => 'nl', 'label' => 'Dutch'),
				array('value' => 'fr', 'label' => 'French'),
				array('value' => 'de', 'label' => 'German'),
				array('value' => 'hu', 'label' => 'Hungarian'),
				array('value' => 'it', 'label' => 'Italian'),
				array('value' => 'ru', 'label' => 'Russian'),
				array('value' => 'es', 'label' => 'Spanish')
			),
			(SpoonSession::exists('languages') ? SpoonSession::get('languages') : 'en')
		);

		// multiple languages (backend)
		$this->frm->addMultiCheckbox('interface_languages',
			array(
				array('value' => 'en', 'label' => 'English'),
				array('value' => 'cn', 'label' => 'Chinese'),
				array('value' => 'nl', 'label' => 'Dutch'),
				array('value' => 'fr', 'label' => 'French'),
				array('value' => 'de', 'label' => 'German'),
				array('value' => 'hu', 'label' => 'Hungarian'),
				array('value' => 'it', 'label' => 'Italian'),
				array('value' => 'ru', 'label' => 'Russian'),
				array('value' => 'es', 'label' => 'Spanish')
			),
			(SpoonSession::exists('interface_languages') ? SpoonSession::get('interface_languages') : 'en')
		);

		// single language (frontend)
		$this->frm->addDropdown('language',
			array(
				'en' => 'English',
				'cn' => 'Chinese',
				'nl' => 'Dutch',
				'fr' => 'French',
				'de' => 'German',
				'hu' => 'Hungarian',
				'it' => 'Italian',
				'ru' => 'Russian',
				'es' => 'Spanish'
			),
			(SpoonSession::exists('default_language') ? SpoonSession::get('default_language') : 'en')
		);

		// default language (frontend)
		$this->frm->addDropdown('default_language',
			array(
				'en' => 'English',
				'cn' => 'Chinese',
				'nl' => 'Dutch',
				'fr' => 'French',
				'de' => 'German',
				'hu' => 'Hungarian',
				'it' => 'Italian',
				'ru' => 'Russian',
				'es' => 'Spanish'
			),
			(SpoonSession::exists('default_language') ? SpoonSession::get('default_language') : 'en')
		);

		// default language (backend)
		$this->frm->addDropdown('default_interface_language',
			array(
				'en' => 'English',
				'cn' => 'Chinese',
				'nl' => 'Dutch',
				'fr' => 'French',
				'de' => 'German',
				'hu' => 'Hungarian',
				'it' => 'Italian',
				'ru' => 'Russian',
				'es' => 'Spanish'
			),
			(SpoonSession::exists('default_interface_language') ? SpoonSession::get('default_interface_language') : 'en')
		);
	}

	/**
	 * Validate the form based on the variables in $_POST
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
