<?php

/**
 * BackendDatagrid,
 * this is our extended version of SpoonDatagrid
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
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendDataGrid extends SpoonDataGrid
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param SpoonDataGridSource $source	The datasource
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

		// add classes on headers
		foreach($this->getColumns() as $column) $this->setColumnHeaderAttributes($column, array('class' => $column));

		// set paging class
		$this->setPagingClass('BackendDatagridPaging');

		// set default template
		$this->setTemplate(BACKEND_CORE_PATH .'/layout/templates/datagrid.tpl');
	}


	/**
	 * Adds a new column
	 *
	 * @return	void
	 * @param	string $name				The name for the new column.
	 * @param	string[optional] $label		The label for the column.
	 * @param	string[optional] $value		The value for the column.
	 * @param	string[optional] $URL		The URL for the link inside the column.
	 * @param	string[optional] $image		An URL to the image inside the column.
	 * @param	int[optional] $sequence		The sequence for the column.
	 */
	public function addColumn($name, $label = null, $value = null, $URL = null, $title = null, $image = null, $sequence = null)
	{
		// known actions that should have a button
		if(in_array($name, array('add', 'edit', 'delete')))
		{
			// rebuild value, it should have special markup
			$value = '<a href="'. $URL .'" class="button icon icon'. SpoonFilter::toCamelCase($name) .' linkButton">
						<span><span><span>'. $value .'</span></span></span>
					</a>';

			// reset URL
			$URL = null;
		}

		if(in_array($name, array('use_revision', 'use_draft')))
		{
			// rebuild value, it should have special markup
			$value = '<a href="'. $URL .'" class="button icon'. SpoonFilter::toCamelCase($name) .'">
						<span><span><span>'. $value .'</span></span></span>
					</a>';

			// reset URL
			$URL = null;

		}

		// add the column
		parent::addColumn($name, $label, $value, $URL, $title, $image, $sequence);

		// known actions
		if(in_array($name, array('add', 'edit', 'delete', 'use_revision', 'use_draft')))
		{
			// add special attributes for actions we know
			$this->setColumnAttributes($name, array('class' => 'action action'. SpoonFilter::toCamelCase($name),
													'width' => '10%'));
		}

		// set header attributes
		$this->setColumnHeaderAttributes($name, array('class' => $name));
	}


	/**
	 * Adds a new column with a custom action button
	 *
	 * @return	void
	 * @param	string $name				The name for the new column.
	 * @param	string[optional] $label		The label for the column.
	 * @param	string[optional] $value		The value for the column.
	 * @param	string[optional] $URL		The URL for the link inside the column.
	 * @param	string[optional] $image		An URL to the image inside the column.
	 * @param	int[optional] $sequence		The sequence for the column.
	 */
	public function addColumnAction($name, $value = null, $label = null, $URL = null, $title = null, $anchorAttributes = null, $image = null, $sequence = null)
	{
		// set anchorAttributes
		if(empty($anchorAttributes)) $anchorAttributes = 'button icon icon'. SpoonFilter::toCamelCase($name) .' linkButton';

		// rebuild value
		$value = '<a href="'. $URL .'" class="'. $anchorAttributes .'">
						<span><span><span>'. $label .'</span></span></span>
					</a>';

		// add the column to the datagrid
		parent::addColumn($name, $label, $value, $URL, $title, $image, $sequence);

		// set column attributes
		$this->setColumnAttributes($name, array('class' => 'action action'. SpoonFilter::toCamelCase($name), 'width' => '10%'));

		// set header attributes
		$this->setColumnHeaderAttributes($name, array('class' => $name));
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
	 * Retrieve the parsed output.
	 *
	 * @return	string
	 */
	public function getContent()
	{
		// mass action was set
		if($this->tpl->getAssignedValue('massAction') !== null) $this->tpl->assign('footer', true);

		// has paging & more than 1 page
		elseif($this->getPaging() && $this->getNumResults() > $this->getPagingLimit()) $this->tpl->assign('footer', true);

		// execute parent
		return parent::getContent();
	}


	/**
 	 * Sets the active tab for this datagrid
 	 *
 	 * @return	void
 	 * @param	string $tab		The name of the tab to show.
	 */
	public function setActiveTab($tab)
	{
		$this->setURL('#'. $tab, true);
	}


	/**
	 * Sets the column function to be executed for every row
	 *
	 * @return	void
	 * @param	mixed $function					The function to execute.
	 * @param	mixed[optional] $arguments		The arguments to pass to the function.
	 * @param	mixed $columns					The column wherin the result will be printed.
	 * @param	bool[optional] $overwrite		Should the orginal value be overwritten.
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
				$headerAttributes = array('class' => 'date');
			break;
		}

		// add attributes if they are given
		if(!empty($attributes))
		{
			// loop and set attributes
			foreach($columns as $column) $this->setColumnAttributes($column, $attributes);
		}

		// add attributes if they are given
		if(!empty($headerAttributes))
		{
			// loop and set attributes
			foreach($columns as $column) $this->setColumnHeaderAttributes($column, $attributes);
		}
	}


	/**
	 * Sets the dropdown for the mass action
	 *
	 * @return	void
	 * @param	SpoonFormDropdown $actionDropDown	A dropdown-instance.
	 */
	public function setMassAction(SpoonFormDropdown $actionDropDown)
	{
		// buid HTML
		$HTML = '<p><label>'. ucfirst(BL::getLabel('WithSelected')) .'</label></p>
				<p>
					'. $actionDropDown->parse() .'
				</p>
				<div class="buttonHolder"><a href="#" class="submitButton button" id="massActionButton"><span><span><span>'. ucfirst(BL::getLabel('Execute')) .'</span></span></span></a></div>';

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
		// default URL
		$this->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting labels
		$this->setSortingLabels(BL::getLabel('SortAscending'), BL::getLabel('SortedAscending'), BL::getLabel('SortDescending'), BL::getLabel('SortedDescending'));
	}


	/**
	 * Sets an URL, optionally only appending the provided piece
	 *
	 * @return	void
	 * @param	string $URL					The URL to set.
	 * @param	bool[optional] $append		Should it be appended to the existing URL.
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
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendDatagridPaging implements iSpoonDataGridPaging
{
	/**
	 * Builds & returns the pagination
	 *
	 * @return	string
	 * @param	string $URL								The URL.
	 * @param	int $offset								The calculated offset.
	 * @param	string $order							The name of the column to sort on.
	 * @param	string $sort							The sorting method, possible values are: asc, desc.
	 * @param	int $numResults							The number of results.
	 * @param	int $numPerPage							The items per page.
	 * @param	bool[optional] $debug					Should debugging be enabled?
	 * @param	string[optional] $compileDirectory		The director for compiled templates.
	 */
	public static function getContent($URL, $offset, $order, $sort, $numResults, $numPerPage, $debug = true, $compileDirectory = null)
	{
		// current page
		$currentPage = ceil($offset / $numPerPage) + 1;

		// number of pages
		$numPages = ceil($numResults / $numPerPage);

		// load template
		$tpl = new SpoonTemplate();

		// if there is just one page we don't want to see any paging, so return an empty string
		if($numPages == 1) return '';

		// compile directory
		if($compileDirectory !== null) $tpl->setCompileDirectory($compileDirectory);
		else $tpl->setCompileDirectory(dirname(__FILE__));

		// force compiling
		$tpl->setForceCompile((bool) $debug);

		// previous URL
		if($currentPage > 1)
		{
			// label & URL
			$previousURL = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset - $numPerPage), $order, $sort), $URL);
			$tpl->assign('previousURL', $previousURL);
		}

		// next URL
		if($currentPage < $numPages)
		{
			// label & URL
			$nextURL = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset + $numPerPage), $order, $sort), $URL);
			$tpl->assign('nextURL', $nextURL);
		}

		$tpl->assign('previousLabel', BL::getLabel('PreviousPage'));
		$tpl->assign('nextLabel', BL::getLabel('NextPage'));

		// limit
		$limit = 7;
		$breakpoint = 4;
		$items = array();

		/**
		 * Less than or 7 pages. We know all the keys, and we put them in the array
		 * that we will use to generate the actual pagination.
		 */
		if($numPages <= $limit)
		{
			for($i = 1; $i <= $numPages; $i++) $items[$i] = $i;
		}

		// more than 7 pages
		else
		{
			// first page
			if($currentPage == 1)
			{
				// [1] 2 3 4 5 6 7 8 9 10 11 12 13
				for($i = 1; $i <= $limit; $i++) $items[$i] = $i;
				$items[$limit + 1] = '...';
			}

			// last page
			elseif($currentPage == $numPages)
			{
				// 1 2 3 4 5 6 7 8 9 10 11 12 [13]
				$items[$numPages -  $limit - 1] = '...';
				for($i = ($numPages - $limit); $i <= $numPages; $i++) $items[$i] = $i;
			}

			// other page
			else
			{
				// 1 2 3 [4] 5 6 7 8 9 10 11 12 13

				// define min & max
				$min = $currentPage - $breakpoint + 1;
				$max = $currentPage + $breakpoint - 1;

				// minimum doesnt exist
				while($min <= 0)
				{
					$min++;
					$max++;
				}

				// maximum doesnt exist
				while($max > $numPages)
				{
					$min--;
					$max--;
				}

				// create the list
				if($min != 1) $items[$min - 1] = '...';
				for($i = $min; $i <= $max; $i++) $items[$i] = $i;
				if($max != $numPages) $items[$max + 1] = '...';
			}
		}

		// init var
		$pages = array();

		// loop pages
		foreach($items as $item)
		{
			// counter
			if(!isset($i)) $i = 0;

			// base details
			$pages[$i]['page'] = false;
			$pages[$i]['currentPage'] = false;
			$pages[$i]['otherPage'] = false;
			$pages[$i]['noPage'] = false;
			$pages[$i]['url'] = '';
			$pages[$i]['pageNumber'] = $item;

			// hellips
			if($item == '...') $pages[$i]['noPage'] = true;

			// regular page
			else
			{
				// show page
				$pages[$i]['page'] = true;

				// current page ?
				if($item == $currentPage) $pages[$i]['currentPage'] = true;

				// other page
				else
				{
					// show the page
					$pages[$i]['otherPage'] = true;

					// URL to this page
					$pages[$i]['url'] = str_replace(array('[offset]', '[order]', '[sort]'), array((($numPerPage * $item) - $numPerPage), $order, $sort), $URL);
				}
			}

			// update counter
			$i++;
		}

		// first key needs to be zero
		$pages = SpoonFilter::arraySortKeys($pages);

		// assign pages
		$tpl->assign('pages', $pages);

		// cough it up
		return $tpl->getContent(BACKEND_CORE_PATH .'/layout/templates/datagrid_paging.tpl');
	}
}


/**
 * BackendDatagridArray
 * A datagrid with an array as source
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
	 * @param	array $array	The data.
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
	 * @param	string $query					The query to retrieve the data.
	 * @param	array[optional] $parameters		The parameters to be used inside the query.
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
 * @package		backend
 * @subpackage	datagrid
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendDataGridFunctions
{
	/**
	 * Formats plain text as HTML, links will be detected, paragraphs will be inserted
	 *
	 * @return	string
	 * @param	string $var		The data to cleanup.
	 */
	public static function cleanupPlainText($var)
	{
		// redefine
		$var = (string) $var;

		// detect links
		$var = SpoonFilter::replaceURLsWithAnchors($var);

		// replace newlines
		$var = str_replace("\r", '', $var);
		$var = preg_replace('/(?<!.)(\r\n|\r|\n){3,}$/m', '', $var);

		// replace br's into p's
		$var = '<p>'. str_replace("\n", '</p><p>', $var) .'</p>';

		// cleanup
		$var = str_replace("\n", '', $var);
		$var = str_replace('<p></p>', '', $var);

		// return
		return $var;
	}


	/**
	 * Format a date as a long representation according the users' settings
	 *
	 * @return	string
	 * @param	int $timestamp		The UNIX-timestamp to format as a human readable date.
	 */
	public static function getLongDate($timestamp)
	{
		// redefine
		$timestamp = (int) $timestamp;

		// if invalid timestamp return an empty string
		if($timestamp <= 0) return '';

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('datetime_format');

		// format the date according the user his settings
		return SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage());
	}


	/**
	 * Get time ago as a string for use in a datagrid
	 *
	 * @return	string
	 * @param	int $timestamp		The UNIX-timestamp to convert in a time-ago-string.
	 */
	public static function getTimeAgo($timestamp)
	{
		// redefine
		$timestamp = (int) $timestamp;

		// get the time ago as a string
		$timeAgo = SpoonDate::getTimeAgo($timestamp, BL::getInterfaceLanguage());

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('datetime_format');

		// return
		return '<abbr title="'. SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage()) .'">'. $timeAgo .'</abbr>';
	}


	/**
	 * Get the HTML for a user to use in a datagrid
	 *
	 * @return	string
	 * @param	int $id		The Id of the user.
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

		// build html
		$html = '<div class="datagridAvatar">' . "\n";
		$html .= '	<div class="avatar av24">' . "\n";
		$html .= '		<div class="avOverlay">&nbsp;</div>'."\n";
		$html .= '		<a href="'. BackendModel::createURLForAction('edit', 'users') . '&id='. $id .'">'."\n";
		$html .= '			<img src="'. FRONTEND_FILES_URL .'/backend_users/avatars/32x32/'. $avatar .'" width="24" height="24" alt="'. $nickname .'" />'."\n";
		$html .= '		</a>'."\n";
		$html .= '	</div>';
		$html .= '	<p><a href="'. BackendModel::createURLForAction('edit', 'users') . '&id='. $id .'">' . $nickname . '</a></p>' . "\n";
		$html .= '</div>';

		// return
		return $html;
	}


	/**
	 * Truncate a string
	 *
	 * @return	string
	 * @param	string $string				The string to truncate.
	 * @param	int $length					The maximumlength for the string.
	 * @param	bool[optional] $useHellip	Should a hellip be appended?
	 */
	public static function truncate($string = null, $length, $useHellip = true)
	{
		// remove special chars
		$string = htmlspecialchars_decode($string);

		// less characters
		if(mb_strlen($string) <= $length) return SpoonFilter::htmlspecialchars($string);

		// more characters
		else
		{
			// hellip is seen as 1 char, so remove it from length
			if($useHellip) $length = $length - 1;

			// get the amount of requested characters
			$string = mb_substr($string, 0, $length);

			// add hellip
			if($useHellip) $string .= '…';

			// return
			return SpoonFilter::htmlspecialchars($string);
		}
	}
}

?>