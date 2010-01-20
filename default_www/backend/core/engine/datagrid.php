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

		// hide the id by default
		if(in_array('id', $this->getColumns())) $this->setColumnsHidden('id');

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
		// known actions that should have a button
		if(in_array($name, array('add', 'edit', 'delete')))
		{
			// rebuild value, it should have special markup
			$value = '<a href="'. $url .'" class="button icon icon'. SpoonFilter::toCamelCase($name) .' linkButton">
						<span><span><span>'. $value .'</span></span></span>
					</a>';

			// reset url
			$url = null;
		}

		if(in_array($name, array('use_revision')))
		{
			// rebuild value, it should have special markup
			$value = '<a href="'. $url .'" class="button icon'. SpoonFilter::toCamelCase($name) .'">
						<span><span><span>'. $value .'</span></span></span>
					</a>';

			// reset url
			$url = null;

		}

		// add the column
		parent::addColumn($name, $label, $value, $url, $title, $image, $sequence);

		// known actions
		if(in_array($name, array('add', 'edit', 'delete', 'use_revision')))
		{
			// add special attributes for actions we know
			$this->setColumnAttributes($name, array('class' => 'action action'. SpoonFilter::toCamelCase($name),
													'width' => '10%'));
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
 	 * Sets the active tab for this datagrid
 	 *
 	 * @return	void
 	 * @param	string $tab
	 */
	public function setActiveTab($tab)
	{
		$this->setURL('#'. $tab, true);
	}


	/**
	 * Sets the column function to be executed for every row
	 *
	 * @return	void
	 * @param	mixed $function
	 * @param	mixed[optional] $arguments
	 * @param	mixed $columns
	 * @param	bool[optional] $overwrite
	 */
	public function setColumnFunction($function, $arguments = null, $columns, $overwrite = true)
	{
		// call the parent
		parent::setColumnFunction($function, $arguments, $columns, $overwrite);

		// redefine columns
		$columns = (array) $columns;
		$attributes = null;

		// based on the function we should prepopulate the attributes array
		switch($function)
		{
			// timeAgo
			case array('BackendDataGridFunctions', 'getTimeAgo'):
				$attributes = array('class' => 'date');
			break;
		}

		// add attributes if they are given
		if(!empty($attributes))
		{
			// loop and set attributes
			foreach($columns as $column) $this->setColumnAttributes($column, $attributes);
		}
	}


	/**
	 * Sets the dropdown for the mass action
	 *
	 * @return	void
	 * @param	SpoonDropDown $actionDropDown
	 */
	public function setMassAction(SpoonDropDown $actionDropDown)
	{
		// buid HTML
		$HTML = '<p><label>'. ucfirst(BL::getLabel('WithSelected')) .'</label></p>
				<p>
					'. $actionDropDown->parse() .'
				</p>
				<p><a href="#" class="submitButton button" id="massActionButton"><span><span><span>'. ucfirst(BL::getLabel('Execute')) .'</span></span></span></a></p>';

		// assign parsed html
		$this->tpl->assign('massAction', $HTML);
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

		// sorting labels
		$this->setSortingLabels(BL::getLabel('SortAscending'), BL::getLabel('SortedAscending'), BL::getLabel('SortDescending'), BL::getLabel('SortedDescending'));
	}


	/**
	 * Sets an URL, optionally only appending the provided piece
	 *
	 * @return	void
	 * @param	string $URL
	 * @param	bool[optional] $append
	 */
	public function setURL($URL, $append = false)
	{
		if($append) parent::setURL(parent::getURL() . $URL);
		else parent::setURL($URL);
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

		// cough it up
		return $tpl->getContent(BACKEND_CORE_PATH .'/layout/templates/datagrid_paging.tpl');
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
	 * Format a date as a long representation according the users' settings
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


	/**
	 * Get time ago as a string for use in a datagrid
	 *
	 * @return	string
	 * @param	int $timestamp
	 */
	public static function getTimeAgo($timestamp)
	{
		// redefine
		$timestamp = (int) $timestamp;

		// get the time ago as a string
		$timeAgo = BackendModel::calculateTimeAgo($timestamp);

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('date_long_format', 'd/m/Y H:i:s');

		// return
		return '<abbr title="'. SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage()) .'">'. $timeAgo .'</abbr>';
	}


	/**
	 * Get the HTML for a user to use in a datagrid
	 *
	 * @return	string
	 * @param	int $id
	 */
	public static function getUser($id)
	{
		// redefine
		$id = (int) $id;

		// create user instance
		$user = new BackendUser($id);

		// get settings
		$avatar = $user->getSetting('avatar', 'no-avatar.gif');
		$nickname = $user->getSetting('nickname');

		// @todo	Johan, why do we need an a-elements wrapped arround?
		// build html
		$html = '<div class="user">'."\n";
		$html .= '	<a href="'. BackendModel::createURLForAction('edit', 'users') . '&id='. $id .'">'."\n";
		$html .= '		<img src="'. FRONTEND_FILES_URL .'/backend_users/avatars/32x32/'. $avatar .'" width="24" height="24" alt="'. $nickname .'" />'."\n";
		$html .= '		'. $nickname ."\n";
		$html .= '	</a>'."\n";
		$html .= '</div>';

		// return
		return $html;
	}
}

?>