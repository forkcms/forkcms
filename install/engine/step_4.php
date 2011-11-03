<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Step 4 of the Fork installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class InstallerStep4 extends InstallerStep
{
	/**
	 * Executes this step.
	 */
	public function execute()
	{
		$this->loadModules();
		$this->loadForm();
		$this->validateForm();
		$this->parseForm();
		$this->tpl->display('layout/templates/step_4.tpl');
	}

	/**
	 * Is this step allowed.
	 *
	 * @return bool
	 */
	public static function isAllowed()
	{
		return InstallerStep3::isAllowed() && isset($_SESSION['default_language']) && isset($_SESSION['default_interface_language']) && isset($_SESSION['multiple_languages']) && isset($_SESSION['languages']) && isset($_SESSION['interface_languages']);
	}

	/**
	 * Loads the form.
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

		// example data
		$this->frm->addCheckbox('example_data', (SpoonSession::exists('example_data') ? SpoonSession::get('example_data') : true));

		// debug mode
		$this->frm->addCheckbox('debug_mode', (SpoonSession::exists('debug_mode') ? SpoonSession::get('debug_mode') : false));
	}

	/**
	 * Scans the directory structure for modules and adds them to the list of optional modules
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
	 */
	private function validateForm()
	{
		// form submitted
		if($this->frm->isSubmitted())
		{
			// all valid
			if($this->frm->isCorrect())
			{
				// get selected modules
				$modules = $this->frm->getField('modules')->getValue();

				// add blog if example data was checked
				if($this->frm->getField('example_data')->getChecked() && !in_array('blog', $modules)) $modules[] = 'blog';

				// set modules
				SpoonSession::set('modules', $modules);

				// example data
				SpoonSession::set('example_data', $this->frm->getField('example_data')->getChecked());

				// debug mode
				SpoonSession::set('debug_mode', $this->frm->getField('debug_mode')->getChecked());

				// redirect
				SpoonHTTP::redirect('index.php?step=5');
			}
		}
	}
}
