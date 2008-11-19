<?php

/**
 * Fork
 *
 * This is the base-object for action-files
 *
 * @package		frontend
 * @subpackage	extra
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBaseAction extends FrontendBaseObject
{
	/**
	 * Extra instance
	 *
	 * @var	FrontendExtra
	 */
	protected $extra;


	/**
	 * The path to the template
	 *
	 * @var	string
	 */
	protected $templatePath;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// call parent
		parent::__construct();

		// set properties
		$this->extra = Spoon::getObjectReference('extra');
	}


	/**
	 * Display the extra
	 * 	This calls the execute function
	 *
	 * @return	void
	 */
	public function display()
	{
		// execute the extra
		$this->execute();

		// assign a variable so the action's template can be included
		$this->tpl->assign('extraTemplatePath', $this->getTemplatePath());
	}


	/**
	 * Get the template-path
	 *
	 * @return	string
	 */
	protected function getTemplatePath()
	{
		return $this->templatePath;
	}


	/**
	 * Load the template
	 *
	 * @return	void
	 */
	protected function loadTemplate()
	{
		$this->setTemplatePath(FRONTEND_MODULE_PATH .'/layout/templates/'. FRONTEND_ACTION .'.tpl');
	}


	/**
	 * Set the template-path
	 *
	 * @return	void
	 * @param	string $path
	 */
	protected function setTemplatePath($path)
	{
		$this->templatePath = (string) $path;
	}
}
?>