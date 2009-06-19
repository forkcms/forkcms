<?php

/**
 * BackendDatagrid, this is our extended version of SpoonDatagrid
 *
 * This class will handle a lot of stuff for you, for example:
 * 	- it will set debugmode
 *	- it will set the compile-directory
 * 	- ...
 *
 * @package		backend
 * @subpackage	datagrid
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendDataGrid extends SpoonDataGrid
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param SpoonDataGridSource $source
	 */
	public function __construct(SpoonDataGridSource $source)
	{
		// call parent constructor
		parent::__construct($source);

		// set debugmode, this will force the recompile for the used templates
		$this->setDebug(SPOON_DEBUG);

		// set the compile-directory, so compiled templates will be in a folder that is writable
		$this->setCompileDirectory(BACKEND_CACHE_PATH .'/templates');

		// set attributes for the datagrid
		$this->setAttributes(array('class' => 'datagrid', 'cellspacing' => 0, 'cellpadding' => 0, 'border' => 1));

		// set default template
		$this->setTemplate(BACKEND_CORE_PATH .'/layout/templates/datagrid.tpl');

		// set default url, you should alter it!
		$this->setURL(BackendModel::createURLForAction());
	}


	/**
	 * Enable drag and drop for the current datagrid
	 *
	 * @return	void
	 */
	public function enableSequenceByDragAndDrop()
	{
		// get header object
		$header = Spoon::getObjectReference('header');

		// add drag and drop-class
		$this->setAttributes(array('class' => 'datagrid sequenceByDragAndDrop'));

		// disable paging
		$this->setPaging(false);

		// hide the sequence column
		$this->setColumnHidden('sequence');

		// add a column for the handle, so users have something to hold while draging
		$this->addColumn('dragAndDropHandle');

		// make sure the column with the handler is the first one
		$this->setColumnsSequence('dragAndDropHandle');

		// add a class on the handler column, so JS knows this is just a handler
		$this->setColumnAttributes('dragAndDropHandle', array('class' => 'dragAndDropHandle'));

		// our JS needs to know an id, so we can send the new order
		$this->setRowAttributes(array('rel' => '[id]'));
	}
}


/**
 * BackendDatagridArray
 * A datagrid with an array as source
 *
 * This source file is part of Fork CMS.
 *
 * @package		backend
 * @subpackage	datagrid
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendDataGridArray extends BackendDataGrid
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	array $array
	 */
	public function __construct(array $array)
	{
		// create a new source-object
		$source = new SpoonDataGridSourceArray($array);

		// call the parent, as in create a new datagrid with the created source
		parent::__construct($source);
	}
}


/**
 * BackendDatagridDB
 * A datagrid with a DB-connection as source
 *
 * This source file is part of Fork CMS.
 *
 * @package		backend
 * @subpackage	datagrid
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendDataGridDB extends BackendDataGrid
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $query
	 * @param	array[optional] $parameters
	 */
	public function __construct($query, $parameters = array())
	{
		// create a new source-object
		$source = new SpoonDataGridSourceDB(BackendModel::getDB(), array($query, (array) $parameters));

		// call the parent, as in create a new datagrid with the created source
		parent::__construct($source);
	}
}


/**
 * BackendDatagridFunctions
 * A set of common used functions that will be applied on rows or columns
 *
 * This source file is part of Fork CMS.
 *
 * @package		backend
 * @subpackage	datagrid
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendDataGridFunctions
{
	/**
	 * Format a date as a long representation according the user his settings
	 *
	 * @return	string
	 * @param	int $timestamp
	 */
	public static function getLongDate($timestamp)
	{
		// redefine
		$timestamp = (int) $timestamp;

		// if invalid timestamp return an empty string
		if($timestamp <= 0) return '';

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('date_long_format', 'd/m/Y H:i:s');

		// format the date according the user his settings
		return SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage());
	}
}

?>