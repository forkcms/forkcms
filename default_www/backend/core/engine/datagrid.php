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
		$this->setAttributes(array('class' => 'datagrid', 'cellspacing' => 0, 'cellpadding' => 0, 'border' => 0));

		// set default sorting options
		$this->setSortingOptions();

		// set paging class
		$this->setPagingClass('BackendDatagridPaging');

		// set default template
		$this->setTemplate(BACKEND_CORE_PATH .'/layout/templates/datagrid.tpl');
	}


	/**
	 * Adds a new column
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $label
	 * @param	string[optional] $value
	 * @param	string[optional] $url
	 * @param	string[optional] $image
	 * @param	int[optional] $sequence
	 */
	public function addColumn($name, $label = null, $value = null, $url = null, $title = null, $image = null, $sequence = null)
	{
		// add the column
		parent::addColumn($name, $label, $value, $url, $title, $image, $sequence);

		// known actions
		if(in_array($name, array('add', 'edit', 'delete')))
		{
			$this->setColumnAttributes($name, array('class' => 'action action'. SpoonFilter::toCamelCase($name)));
		}
	}


	/**
	 * Enable drag and drop for the current datagrid
	 *
	 * @return	void
	 */
	public function enableSequenceByDragAndDrop()
	{
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


	/**
	 * Sets all the default settings needed when attempting to use sorting
	 *
	 * @return	void
	 */
	private function setSortingOptions()
	{
		// default url
		$this->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting icons, used to click on
		$this->setSortingIcons('/backend/core/layout/images/icons/sort_ascending.gif', '/backend/core/layout/images/icons/sorted_ascending.gif', '/backend/core/layout/images/icons/sort_descending.gif', '/backend/core/layout/images/icons/sorted_descending.gif');

		// sorting labels
		$this->setSortingLabels(BL::getLabel('SortAscending'), BL::getLabel('SortedAscending'), BL::getLabel('SortDescending'), BL::getLabel('SortedDescending'));
	}
}


/**
 * BackendDatagridPaging
 *
 * @package		backend
 * @subpackage	datagrid
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendDatagridPaging implements iSpoonDataGridPaging
{
	/**
	 * Builds & returns the pagination
	 *
	 * @return	string
	 * @param	string $url
	 * @param	int $offset
	 * @param	string $order
	 * @param	string $sort
	 * @param	int $numResults
	 * @param	int $numPerPage
	 * @param	bool[optional] $debug
	 * @param	string[optional] $compileDirectory
	 */
	public static function getContent($url, $offset, $order, $sort, $numResults, $numPerPage, $debug = true, $compileDirectory = null)
	{
		// current page
		$iCurrentPage = ceil($offset / $numPerPage) + 1;

		// number of pages
		$iPages = ceil($numResults / $numPerPage);

		// load template
		$tpl = new SpoonTemplate();

		// compile directory
		if($compileDirectory !== null) $tpl->setCompileDirectory($compileDirectory);
		else $tpl->setCompileDirectory(dirname(__FILE__));

		// force compiling
		$tpl->setForceCompile((bool) $debug);

		// previous url
		if($iCurrentPage > 1)
		{
			// label & url
			$previousURL = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset - $numPerPage), $order, $sort), $url);
			$tpl->assign('previousURL', $previousURL);
		}

		// next url
		if($iCurrentPage < $iPages)
		{
			// label & url
			$nextURL = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset + $numPerPage), $order, $sort), $url);
			$tpl->assign('nextURL', $nextURL);
		}

		$tpl->assign('previousLabel', BL::getLabel('PreviousPage'));
		$tpl->assign('nextLabel', BL::getLabel('NextPage'));

		// limit
		$limit = 7;
		$breakpoint = 4;
		$aItems = array();

		/**
		 * Less than or 7 pages. We know all the keys, and we put them in the array
		 * that we will use to generate the actual pagination.
		 */
		if($iPages <= $limit)
		{
			for($i = 1; $i <= $iPages; $i++) $aItems[$i] = $i;
		}

		// more than 7 pages
		else
		{
			// first page
			if($iCurrentPage == 1)
			{
				// [1] 2 3 4 5 6 7 8 9 10 11 12 13
				for($i = 1; $i <= $limit; $i++) $aItems[$i] = $i;
				$aItems[$limit + 1] = '...';
			}

			// last page
			elseif($iCurrentPage == $iPages)
			{
				// 1 2 3 4 5 6 7 8 9 10 11 12 [13]
				$aItems[$iPages -  $limit - 1] = '...';
				for($i = ($iPages - $limit); $i <= $iPages; $i++) $aItems[$i] = $i;
			}

			// other page
			else
			{
				// 1 2 3 [4] 5 6 7 8 9 10 11 12 13

				// define min & max
				$min = $iCurrentPage - $breakpoint + 1;
				$max = $iCurrentPage + $breakpoint - 1;

				// minimum doesnt exist
				while($min <= 0)
				{
					$min++;
					$max++;
				}

				// maximum doesnt exist
				while($max > $iPages)
				{
					$min--;
					$max--;
				}

				// create the list
				if($min != 1) $aItems[$min - 1] = '...';
				for($i = $min; $i <= $max; $i++) $aItems[$i] = $i;
				if($max != $iPages) $aItems[$max + 1] = '...';
			}
		}

		// init var
		$aPages = array();

		// loop pages
		foreach($aItems as $item)
		{
			// counter
			if(!isset($i)) $i = 0;

			// base details
			$aPages[$i]['page'] = false;
			$aPages[$i]['currentPage'] = false;
			$aPages[$i]['otherPage'] = false;
			$aPages[$i]['noPage'] = false;
			$aPages[$i]['url'] = '';
			$aPages[$i]['pageNumber'] = $item;

			// hellips
			if($item == '...') $aPages[$i]['noPage'] = true;

			// regular page
			else
			{
				// show page
				$aPages[$i]['page'] = true;

				// current page ?
				if($item == $iCurrentPage) $aPages[$i]['currentPage'] = true;

				// other page
				else
				{
					// show the page
					$aPages[$i]['otherPage'] = true;

					// url to this page
					$aPages[$i]['url'] = str_replace(array('[offset]', '[order]', '[sort]'), array((($numPerPage * $item) - $numPerPage), $order, $sort), $url);
				}
			}

			// update counter
			$i++;
		}

		// first key needs to be zero
		$aPages = SpoonFilter::arraySortKeys($aPages);

		// assign pages
		$tpl->assign('pages', $aPages);

		// @todo davy - implementeren van $tpl->getContent()

		// cough it up
		ob_start();
		$tpl->display(BACKEND_CORE_PATH .'/layout/templates/datagrid_paging.tpl');
		return ob_get_clean();
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