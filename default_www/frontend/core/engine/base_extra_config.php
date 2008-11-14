<?php
/**
 * Fork
 *
 * This is the base-object for config-files
 *
 * @package		frontend
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendExtraBaseConfig extends FrontendBaseObject
{
	/**
	 * All the possible actions
	 *
	 * @var	array
	 */
	protected $aPossibleActions = array();


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// call parent
		parent::__construct();
	}


	/**
	 * Get the filename for an action
	 *
	 * @return	string
	 * @param	string $action
	 * @param	string $className
	 */
	public function getActionFileName($action, $className)
	{
		// no action?
		if($action === null) $action = constant($className .'::DEFAULT_ACTION');

		// cleanup
		$action = SpoonFilter::toCamelCase($action);

		// search the file
		$fileName = array_search($action, $this->aPossibleActions);

		// validate
		if($fileName === false) throw new FrontendException('Invalid action ('. $action .').');

		// return the filename
		return $fileName;
	}


	/**
	 * Get the action name
	 *
	 * @return	void
	 * @param	string $action
	 * @param	string $className
	 */
	public function getActionName($action, $className)
	{
		// no action?
		if($action === null) $action = constant($className .'::DEFAULT_ACTION');

		// cleanup and return
		return SpoonFilter::toCamelCase($action);
	}


	/**
	 * Set the possible actions, based on files in folder
	 *
	 * @return	void
	 */
	public function setPossibleActions()
	{
		// @todo	find a way to disable certain actions

		// get filelist
		$aActionFiles = (array) SpoonFile::getList(FRONTEND_MODULE_PATH .'/actions');

		// loop files
		foreach ($aActionFiles as $file) $actions[$file] = SpoonFilter::toCamelCase(str_replace('.php', '', $file));

		// set actions
		$this->aPossibleActions = $actions;
	}
}
?>