<?php

// de basis voor alle stappen
class InstallerStep
{
	/**
	 * Form
	 *
	 * @var SpoonForm
	 */
	protected $frm;


	/**
	 * List of all modules (required, hidden and found on the filesystem).
	 * Keep in mind that the order of the required modules is the actual order in which we're going to install these modules.
	 *
	 * @var array
	 */
	protected $modules = array('required' => array('locale', 'users', 'example', 'settings', 'pages', 'search', 'contact', 'content_blocks', 'tags'),
								'hidden' => array('authentication', 'dashboard', 'error'),
								'optional' => array());


	/**
	 * Step number
	 *
	 * @var	int
	 */
	protected $step;


	/**
	 * Template
	 *
	 * @var	SpoonTemplate
	 */
	protected $tpl;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	int $step
	 */
	public function __construct($step)
	{
		// set setp
		$this->step = (int) $step;

		// skip step 1
		if($this->step > 1)
		{
			// include path
			set_include_path($_SESSION['path_library'] . PATH_SEPARATOR . get_include_path());

			// load spoon
			require_once $_SESSION['path_library'] .'/spoon/spoon.php';

			// create template
			$this->tpl = new SpoonTemplate();
			$this->tpl->setForceCompile(true);

			// create form
			$this->frm = new SpoonForm('step'. $step, 'index.php?step='. $step);
		}
	}


	/**
	 * Fetch a module setting
	 *
	 * @return	mixed
	 * @param	string $module
	 * @param	string $name
	 */
	private function getSetting($module, $name)
	{
	}


	/**
	 * Loads spoon library
	 *
	 * @return	void
	 * @param	string $pathLibrary
	 */
	protected function loadSpoon($pathLibrary)
	{
		require_once $pathLibrary .'/spoon/spoon.php';
	}


	/**
	 * Parses the form into the template
	 *
	 * @return	void
	 */
	protected function parseForm()
	{
		if($this->step > 1) $this->frm->parse($this->tpl);
	}


	/**
	 * Stores a module setting
	 *
	 * @return	void
	 * @param	string $module
	 * @param	string $name
	 * @param	string[optional] $value
	 */
	private function setSetting($module, $name, $value = null)
	{
	}
}

?>