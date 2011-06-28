<?php

/**
 * This is our extended version of SpoonDataGrid
 *
 * This class will handle a lot of stuff for you, for example:
 * 	- it will set debugmode
 *	- it will set the compile-directory
 * 	- ...
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		2.0
 */
class BackendDataGrid extends SpoonDataGrid
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	SpoonDataGridSource $source	The datasource.
	 */
	public function __construct(SpoonDataGridSource $source)
	{
		// call parent constructor
		parent::__construct($source);

		// set debugmode, this will force the recompile for the used templates
		$this->setDebug(SPOON_DEBUG);

		// set the compile-directory, so compiled templates will be in a folder that is writable
		$this->setCompileDirectory(BACKEND_CACHE_PATH . '/compiled_templates');

		// set attributes for the datagrid
		$this->setAttributes(array('class' => 'dataGrid', 'cellspacing' => 0, 'cellpadding' => 0, 'border' => 0));

		// hide the id by default
		if(in_array('id', $this->getColumns())) $this->setColumnsHidden('id');

		// set default sorting options
		$this->setSortingOptions();

		// add classes on headers
		foreach($this->getColumns() as $column)
		{
			// set class
			$this->setColumnHeaderAttributes($column, array('class' => $column));

			// set default label
			$this->setHeaderLabels(array($column => ucfirst(BL::lbl(SpoonFilter::toCamelCase($column)))));
		}

		// set paging class
		$this->setPagingClass('BackendDataGridPaging');

		// our JS needs to know an id, so we can highlight it
		$this->setRowAttributes(array('id' => 'row-[id]'));

		// set default template
		$this->setTemplate(BACKEND_CORE_PATH . '/layout/templates/datagrid.tpl');
	}


	/**
	 * Adds a new column
	 *
	 * @return	void
	 * @param	string $name				The name for the new column.
	 * @param	string[optional] $label		The label for the column.
	 * @param	string[optional] $value		The value for the column.
	 * @param	string[optional] $URL		The URL for the link inside the column.
	 * @param	string[optional] $title		A title for the image inside the column.
	 * @param	string[optional] $image		An URL to the image inside the column.
	 * @param	int[optional] $sequence		The sequence for the column.
	 */
	public function addColumn($name, $label = null, $value = null, $URL = null, $title = null, $image = null, $sequence = null)
	{
		// known actions that should have a button
		if(in_array($name, array('add', 'edit', 'delete', 'details', 'approve', 'mark_as_spam')))
		{
			// rebuild value, it should have special markup
			$value = '<a href="' . $URL . '" class="button icon icon' . SpoonFilter::toCamelCase($name) . ' linkButton">
						<span>' . $value . '</span>
					</a>';

			// reset URL
			$URL = null;
		}

		if(in_array($name, array('use_revision', 'use_draft')))
		{
			// rebuild value, it should have special markup
			$value = '<a href="' . $URL . '" class="button icon' . SpoonFilter::toCamelCase($name) . '">
						<span>' . $value . '</span>
					</a>';

			// reset URL
			$URL = null;

		}

		// add the column
		parent::addColumn($name, $label, $value, $URL, $title, $image, $sequence);

		// known actions
		if(in_array($name, array('add', 'edit', 'delete', 'details', 'approve', 'mark_as_spam', 'use_revision', 'use_draft')))
		{
			// add special attributes for actions we know
			$this->setColumnAttributes($name, array('class' => 'action action' . SpoonFilter::toCamelCase($name)));
		}

		// set header attributes
		$this->setColumnHeaderAttributes($name, array('class' => $name));
	}


	/**
	 * Adds a new column with a custom action button
	 *
	 * @return	void
	 * @param	string $name						The name for the new column.
	 * @param	string[optional] $label				The label for the column.
	 * @param	string[optional] $value				The value for the column.
	 * @param	string[optional] $URL				The URL for the link inside the column.
	 * @param	string[optional] $title				The title for the link inside the column.
	 * @param	array[optional] $anchorAttributes	The attributes for the anchor inside the column.
	 * @param	string[optional] $image				An URL to the image inside the column.
	 * @param	int[optional] $sequence				The sequence for the column.
	 */
	public function addColumnAction($name, $label = null, $value = null, $URL = null, $title = null, $anchorAttributes = null, $image = null, $sequence = null)
	{
		// reserve var for attributes
		$attributes = '';

		// no anchorAttributes set means we set the default class attribute for the anchor
		if(empty($anchorAttributes)) $anchorAttributes['class'] = 'button icon icon' . SpoonFilter::toCamelCase($name) . ' linkButton';

		// loop the attributes, build our attributes string
		foreach($anchorAttributes as $attribute => $attributeValue) $attributes .= ' ' . $attribute . '="' . $attributeValue . '"';

		// rebuild value
		$value = '<a href="' . $URL . '"' . $attributes . '>
						<span>' . $value . '</span>
					</a>';

		// add the column to the datagrid
		parent::addColumn($name, $label, $value, null, $title, $image, $sequence);

		// set column attributes
		$this->setColumnAttributes($name, array('class' => 'action action' . SpoonFilter::toCamelCase($name),
												'style' => 'width: 10%;'));

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
		$this->setAttributes(array('class' => 'dataGrid sequenceByDragAndDrop'));

		// disable paging
		$this->setPaging(false);

		// hide the sequence column
		$this->setColumnHidden('sequence');

		// add a column for the handle, so users have something to hold while draging
		$this->addColumn('dragAndDropHandle', null, '<span>' . BL::lbl('Move') . '</span>');

		// make sure the column with the handler is the first one
		$this->setColumnsSequence('dragAndDropHandle');

		// add a class on the handler column, so JS knows this is just a handler
		$this->setColumnAttributes('dragAndDropHandle', array('class' => 'dragAndDropHandle'));

		// our JS needs to know an id, so we can send the new order
		$this->setRowAttributes(array('data-id' => '[id]'));
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
		$this->setURL('#' . $tab, true);
	}


	/**
	 * Set a custom column confirm message.
	 *
	 * @return	void
	 * @param	string $column					The name of the column to set the confirm for.
	 * @param	string $message					The message to use as a confirmmessage.
	 * @param	string[optional] $custom		Unused parameter.
	 * @param	string[optional] $title			The title for the column.
	 * @param	string[optional] $uniqueId		A unique ID that will be uses.
	 */
	public function setColumnConfirm($column, $message, $custom = null, $title = null, $uniqueId = '[id]')
	{
		// redefine
		$column = (string) $column;
		$message = (string) $message;
		$custom = $custom;
		$title = ($title !== null) ? (string) $title : null;
		$uniqueId = (string) $uniqueId;

		// has results
		if($this->source->getNumResults() > 0)
		{
			// column doesnt exist
			if(!isset($this->columns[$column])) throw new SpoonDataGridException('The column "' . $column . '" doesn\'t exist, therefore no confirm message/script can be added.');

			// exists
			else
			{
				// get URL
				$URL = $this->columns[$column]->getURL();

				// URL provided?
				if($URL != '')
				{
					// grab current value
					$currentValue = $this->columns[$column]->getValue();

					// reset URL
					$this->columns[$column]->setURL(null);

					// set the value
					$this->columns[$column]->setValue('<a href="' . $URL . '" class="">' . $currentValue . '</a>');
				}

				// generate id
				$id = 'confirm-' . (string) $uniqueId;

				// set title if there wasn't one provided
				if($title === null) $title = ucfirst(BL::lbl('Delete') . '?');

				// grab current value
				$value = $this->columns[$column]->getValue();

				// add class for confirmation
				if(substr_count($value, '<a') > 0)
				{
					if(substr_count($value, 'class="') > 0) $value = str_replace('class="', 'data-message-id="' . $id . '" class="askConfirmation ', $value);
					else $value = str_replace('<a ', '<a data-message-id="' . $id . '" class="askConfirmation" ', $value);
				}

				// is it a link?
				else throw new BackendException('The column doesn\'t contain a link.');

				// append message
				$value .= '<div id="' . $id . '" title="' . $title . '" style="display: none;"><p>' . $message . '</p></div>';

				// reset value
				$this->columns[$column]->setValue($value);
			}
		}
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
		$HTML = '<p><label for="' . $actionDropDown->getAttribute('id') . '">' . ucfirst(BL::lbl('WithSelected')) . '</label></p>
				<p>
					' . $actionDropDown->parse() . '
				</p>
				<div class="buttonHolder">
					<a href="#" class="submitButton button">
						<span>' . ucfirst(BL::lbl('Execute')) . '</span>
					</a>
				</div>';

		// assign parsed html
		$this->tpl->assign('massAction', $HTML);
	}


	/**
	 * Sets the checkboxes for the mass action
	 *
	 * @return	void
	 * @param	string $column						The name for the column that will hold the checkboxes.
	 * @param	string $value						The value for the checkbox.
	 * @param	array[optional] $excludedValues		The values that should be excluded.
	 * @param	array[optional] $checkedValues		The values that should be checked.
	 */
	public function setMassActionCheckboxes($column, $value, array $excludedValues = null, array $checkedValues = null)
	{
		// build label and value
		$label = '<span class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></span>';
		$value = '<span><input type="checkbox" name="id[]" value="' . $value . '" class="inputCheckbox" /></span>';

		// add the column
		$this->addColumn($column, $label, $value);

		// set as first column
		$this->setColumnsSequence($column);

		// excluded IDs found
		if(!empty($excludedValues))
		{
			// fetch the datagrid attributes
			$attributes = $this->getAttributes();

			// set if needed
			if(!isset($attributes['id'])) $this->setAttributes(array('id' => 'table_' . time()));

			// fetch the datagrid attributes
			$attributes = $this->getAttributes();

			// build array
			$excludedData['id'] = $attributes['id'];
			$excludedData['JSON'] = json_encode($excludedValues);

			// assign the stack to the datagrid template
			$this->tpl->assign('excludedCheckboxesData', $excludedData);
		}

		// checked IDs found
		if(!empty($checkedValues))
		{
			// fetch the datagrid attributes
			$attributes = $this->getAttributes();

			// set if needed
			if(!isset($attributes['id'])) $this->setAttributes(array('id' => 'table_' . time()));

			// fetch the datagrid attributes
			$attributes = $this->getAttributes();

			// build array
			$checkedData['id'] = $attributes['id'];
			$checkedData['JSON'] = json_encode($checkedValues);

			// assign the stack to the datagrid template
			$this->tpl->assign('checkedCheckboxesData', $checkedData);
		}
	}


	/**
	 * Sets all the default settings needed when attempting to use sorting
	 *
	 * @return	void
	 */
	private function setSortingOptions()
	{
		// default URL
		if(Spoon::exists('url')) $this->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting labels
		$this->setSortingLabels(BL::lbl('SortAscending'), BL::lbl('SortedAscending'), BL::lbl('SortDescending'), BL::lbl('SortedDescending'));
	}


	/**
	 * Set a tooltip
	 *
	 * @return	void
	 * @param	string $column					The name of the column to set the tooltop for.
	 * @param	string $message					The key for the message (will be parsed through BL::msg).
	 */
	public function setTooltip($column, $message)
	{
		// get the column
		$instance = $this->getColumn($column);

		// build the value for the tooltip
		$value = BL::msg($message);

		// reset the label
		$instance->setLabel($instance->getLabel() . '<abbr class="help">?</abbr><span class="tooltip hidden" style="display: none;">' . $value . '</span>');
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
 * This is our implementation of iSpoonDatagGridPaging
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendDataGridPaging implements iSpoonDataGridPaging
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
		// if there is just one page we don't need paging
		if($numResults < $numPerPage) return '';

		// load template
		$tpl = new SpoonTemplate();

		// compile directory
		if($compileDirectory !== null) $tpl->setCompileDirectory($compileDirectory);
		else $tpl->setCompileDirectory(dirname(__FILE__));

		// force compiling
		$tpl->setForceCompile((bool) $debug);

		// init vars
		$pagination = null;
		$showFirstPages = false;
		$showLastPages = false;

		// current page
		$currentPage = ceil($offset / $numPerPage) + 1;

		// number of pages
		$numPages = ceil($numResults / $numPerPage);

		// populate count fields
		$pagination['num_pages'] = $numPages;
		$pagination['current_page'] = $currentPage;

		// as long as we are below page 7 we should show all pages starting from 1
		if($currentPage < 8)
		{
			// init vars
			$pagesStart = 1;
			$pagesEnd = ($numPages >= 7) ? 7 : $numPages;

			// show last pages
			if($numPages > 8) $showLastPages = true;
		}

		// as long as we are 7 pages from the end we should show all pages till the end
		elseif($currentPage > ($numPages - 7))
		{
			// init vars
			$pagesStart = ($numPages == 9) ? ($numPages - 6) : ($numPages - 7);
			$pagesEnd = $numPages;

			// show first pages
			$showFirstPages = true;
		}

		// page 7
		else
		{
			// init vars
			$pagesStart = $currentPage - 2;
			$pagesEnd = $currentPage + 2;
			$showFirstPages = true;
			$showLastPages = true;
		}

		// show previous
		if($currentPage > 1)
		{
			// set
			$pagination['show_previous'] = true;
			$pagination['previous_url'] = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset - $numPerPage), $order, $sort), $URL);
		}

		// show first pages?
		if($showFirstPages)
		{
			// init var
			$pagesFirstStart = 1;
			$pagesFirstEnd = 2;

			// loop pages
			for($i = $pagesFirstStart; $i <= $pagesFirstEnd; $i++)
			{
				// add
				$pagination['first'][] = array('url' => str_replace(array('[offset]', '[order]', '[sort]'), array((($numPerPage * $i) - $numPerPage), $order, $sort), $URL),
												'label' => $i);
			}
		}

		// build array
		for($i = $pagesStart; $i <= $pagesEnd; $i++)
		{
			// init var
			$current = ($i == $currentPage);

			// add
			$pagination['pages'][] = array('url' => str_replace(array('[offset]', '[order]', '[sort]'), array((($numPerPage * $i) - $numPerPage), $order, $sort), $URL),
											'label' => $i, 'current' => $current);
		}

		// show last pages?
		if($showLastPages)
		{
			// init var
			$pagesLastStart = $numPages - 1;
			$pagesLastEnd = $numPages;

			// loop pages
			for($i = $pagesLastStart; $i <= $pagesLastEnd; $i++)
			{
				// add
				$pagination['last'][] = array('url' => str_replace(array('[offset]', '[order]', '[sort]'), array((($numPerPage * $i) - $numPerPage), $order, $sort), $URL),
												'label' => $i);
			}
		}

		// show next
		if($currentPage < $numPages)
		{
			// set
			$pagination['show_next'] = true;
			$pagination['next_url'] = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset + $numPerPage), $order, $sort), $URL);
		}

		// multiple pages
		$pagination['multiple_pages'] = ($numPages == 1) ? false : true;

		// assign pagination
		$tpl->assign('pagination', $pagination);

		// assign labels
		$tpl->assign('previousLabel', BL::lbl('PreviousPage'));
		$tpl->assign('nextLabel', BL::lbl('NextPage'));
		$tpl->assign('goToLabel', BL::lbl('GoToPage'));

		// cough it up
		return $tpl->getContent(BACKEND_CORE_PATH . '/layout/templates/datagrid_paging.tpl');
	}
}


/**
 * A datagrid with an array as source
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@netlash.com>
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
 * A datagrid with a DB-connection as source
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendDataGridDB extends BackendDataGrid
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $query						The query to retrieve the data.
	 * @param	array[optional] $parameters			The parameters to be used inside the query.
	 * @param	string[optional] $resultsQuery		The optional count query, used to calculate the number of results.
	 * @param	array[optional] $resultsParameters 	Theh parameters to be used inside the results query.
	 */
	public function __construct($query, $parameters = array(), $resultsQuery = null, $resultsParameters = array())
	{
		// results query?
		$results = ($resultsQuery !== null) ? array($resultsQuery, $resultsParameters) : null;

		// create a new source-object
		$source = new SpoonDataGridSourceDB(BackendModel::getDB(), array($query, (array) $parameters), $results);

		// call the parent, as in create a new datagrid with the created source
		parent::__construct($source);
	}
}


/**
 * A set of common used functions that will be applied on rows or columns
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
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
		$var = '<p>' . str_replace("\n", '</p><p>', $var) . '</p>';

		// cleanup
		$var = str_replace("\n", '', $var);
		$var = str_replace('<p></p>', '', $var);

		// return
		return $var;
	}


	/**
	 * Format a number as a float
	 *
	 * @return	string
	 * @param	float $number				The number to format.
	 * @param	int[optional] $decimals		The number of decimals.
	 */
	public static function formatFloat($number, $decimals = 2)
	{
		// redefine
		$number = (float) $number;
		$decimals = (int) $decimals;

		return number_format($number, $decimals, '.', ' ');
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

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('datetime_format');

		// get the time ago as a string
		$timeAgo = SpoonDate::getTimeAgo($timestamp, BL::getInterfaceLanguage(), $format);

		// return
		return '<abbr title="' . SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage()) . '">' . $timeAgo . '</abbr>';
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
		$html = '<div class="dataGridAvatar">' . "\n";
		$html .= '	<div class="avatar av24">' . "\n";
		$html .= '		<a href="' . BackendModel::createURLForAction('edit', 'users') . '&amp;id=' . $id . '">' . "\n";
		$html .= '			<img src="' . FRONTEND_FILES_URL . '/backend_users/avatars/32x32/' . $avatar . '" width="24" height="24" alt="' . $nickname . '" />' . "\n";
		$html .= '		</a>' . "\n";
		$html .= '	</div>';
		$html .= '	<p><a href="' . BackendModel::createURLForAction('edit', 'users') . '&amp;id=' . $id . '">' . $nickname . '</a></p>' . "\n";
		$html .= '</div>';

		// return
		return $html;
	}


	/**
	 * Returns an image tag
	 *
	 * @return	string
	 * @param	string $path				The path to the image.
	 * @param	string $image				The filename of the image.
	 * @param	string[optional] $title		The title (will be used as alt).
	 */
	public static function showImage($path, $image, $title = '')
	{
		// redefine
		$path = (string) $path;
		$image = (string) $image;
		$title = (string) $title;

		// return
		return '<img src="' . $path . '/' . $image . '" alt="' . $title . '" />';
	}


	/**
	 * Truncate a string
	 *
	 * @return	string
	 * @param	string[optional] $string	The string to truncate.
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
