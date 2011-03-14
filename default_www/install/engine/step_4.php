<?php

/**
 * Step 4 of the Fork installer
 *
 * @package		install
 * @subpackage	installer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
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
		// loads the modules array
		$this->loadModules();

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
		return (isset($_SESSION['db_hostname']) && isset($_SESSION['db_database']) && isset($_SESSION['db_username']) && isset($_SESSION['db_password']));
	}


	/**
	 * Loads the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// init var
		$modules = array();
		$checkedModules = (SpoonSession::exists('modules')) ? SpoonSession::get('modules') : array();

		// loop required modules
		foreach($this->modules['required'] as $module)
		{
			// add to the list
			$modules[] = array('label' => SpoonFilter::toCamelCase($module), 'value' => $module, 'attributes' => array('disabled' => 'disabled'));

			// update $_POST if needed
			if(!isset($_POST['modules']) || !is_array($_POST['modules']) || !in_array($module, $_POST['modules'])) $_POST['modules'][] = $module;
		}

		// loop optional modules
		foreach($this->modules['optional'] as $module)
		{
			// add to the list
			$modules[] = array('label' => SpoonFilter::toCamelCase($module), 'value' => $module);
		}

		// add multi checkbox
		$this->frm->addMultiCheckbox('modules', $modules, array_unique(array_merge($this->modules['required'], $checkedModules)));

		// multiple or single language
		$this->frm->addRadiobutton('languageType',	array(array('value' => 'multiple', 'label' => 'Multiple languages', 'variables' => array('multiple' => true)),
													array('value' => 'single', 'label' => 'Just one language', 'variables' => array('single' => true))), (SpoonSession::exists('multiple_languages') && SpoonSession::get('multiple_languages')) ? 'multiple' : 'single');

		// multiple languages
		$this->frm->addMultiCheckbox('languages', array(array('value' => 'en', 'label' => 'English'),
														array('value' => 'fr', 'label' => 'French'),
														array('value' => 'nl', 'label' => 'Dutch')), (SpoonSession::exists('languages') ? SpoonSession::get('languages') : 'nl'));

		// single languages
		$this->frm->addDropdown('language', array('en' => 'English', 'fr' => 'French', 'nl' => 'Dutch'), (SpoonSession::exists('default_language') ? SpoonSession::get('default_language') : 'en'));

		// single languages
		$this->frm->addDropdown('default_language', array('en' => 'English', 'fr' => 'French', 'nl' => 'Dutch'), (SpoonSession::exists('default_language') ? SpoonSession::get('default_language') : 'en'));

		// example data
		$this->frm->addCheckbox('example_data', (SpoonSession::exists('example_data') ? SpoonSession::get('example_data') : true));
	}


	/**
	 * Scans the directory structure for modules and adds them to the list of optional modules
	 *
	 * @return	void
	 */
	private function loadModules()
	{
		// fetch modules
		$tmpModules = SpoonDirectory::getList(PATH_WWW . '/backend/modules', false, null, '/^[a-z0-9_]+$/i');

		// loop modules
		foreach($tmpModules as $module)
		{
			// not required nor hidden
			if(!in_array($module, $this->modules['required']) && !in_array($module, $this->modules['hidden']))
			{
				// add to the list of optional installs
				$this->modules['optional'][] = $module;
			}
		}
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
			if($this->frm->getField('languageType')->getValue() == 'multiple')
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

			// all valid
			if($this->frm->isCorrect())
			{
				// get selected modules
				$modules = $this->frm->getField('modules')->getValue();

				// add blog if example data was checked
				if($this->frm->getField('example_data')->getChecked() && !in_array('blog', $modules)) $modules[] = 'blog';

				// set modules
				SpoonSession::set('modules', $modules);

				// get default language
				if($this->frm->getField('languageType')->getValue() == 'multiple') $defaultLanguage = $this->frm->getField('default_language')->getValue();
				else $defaultLanguage = $this->frm->getField('default_language')->getValue();

				// set languages
				SpoonSession::set('default_language', $defaultLanguage);
				SpoonSession::set('multiple_languages', ($this->frm->getField('languageType')->getValue() == 'multiple') ? true : false);
				SpoonSession::set('languages', ($this->frm->getField('languageType')->getValue() == 'multiple') ? $this->frm->getField('languages')->getValue() : array($this->frm->getField('default_language')->getValue()));
				SpoonSession::set('example_data', $this->frm->getField('example_data')->getChecked());

				// redirect
				SpoonHTTP::redirect('index.php?step=5');
			}
		}
	}
}

?>