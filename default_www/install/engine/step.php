<?php

/**
 * The base-class for all installer-steps
 *
 * @package		install
 * @subpackage	installer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
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
	protected $modules = array('required' => array('locale', 'users', 'groups', 'example', 'settings', 'pages', 'search', 'content_blocks', 'tags'),
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
	 * @param	int $step	The step to load.
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
			require_once $_SESSION['path_library'] . '/spoon/spoon.php';

			// create template
			$this->tpl = new SpoonTemplate();
			$this->tpl->setForceCompile(true);
			$this->tpl->setCompileDirectory(dirname(__FILE__) . '/../cache/');

			// assign the path
			if(defined('PATH_WWW')) $this->tpl->assign('PATH_WWW', PATH_WWW);

			// create form
			$this->frm = new SpoonForm('step' . $step, 'index.php?step=' . $step);
			$this->frm->setParameter('class', 'forkForms submitWithLink');
			$this->frm->setParameter('id', 'installForm');
		}
	}


	/**
	 * Loads spoon library
	 *
	 * @return	void
	 * @param	string $pathLibrary		The path of the library.
	 */
	protected function loadSpoon($pathLibrary)
	{
		require_once $pathLibrary . '/spoon/spoon.php';
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
}

?>