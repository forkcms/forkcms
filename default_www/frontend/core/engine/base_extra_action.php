<?php

/**
 * Fork
 *
 * This is the base-object for action-files
 *
 * @package		frontend
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBaseAction extends FrontendBaseObject
{
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

		// @todo	set the template
		$this->setTemplatePath(FRONTEND_MODULE_PATH .'/layout/templates/overview.tpl');
	}


	/**
	 * Display the extra
	 * 	This calls the execute function
	 *
	 * @return	void
	 */
	public function display()
	{
		// assign a variable so the action's template can be included
		$this->tpl->assign('extraTemplatePath', $this->getTemplatePath());

		// execute the extra
		$this->execute();
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