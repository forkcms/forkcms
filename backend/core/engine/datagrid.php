<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of SpoonDataGrid
 * This class will handle a lot of stuff for you, for example:
 * 	- it will set debugmode
 *	- it will set the compile-directory
 * 	- ...
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendDataGrid extends SpoonDataGrid
{
	/**
	 * @param SpoonDataGridSource $source
	 */
	public function __construct(SpoonDataGridSource $source)
	{
		parent::__construct($source);

		// set debugmode, this will force the recompile for the used templates
		$this->setDebug(SPOON_DEBUG);

		// set the compile-directory, so compiled templates will be in a folder that is writable
		$this->setCompileDirectory(BACKEND_CACHE_PATH . '/compiled_templates');

		// set attributes for the datagrid
		$this->setAttributes(array('class' => 'dataGrid', 'cellspacing' => 0, 'cellpadding' => 0, 'border' => 0));

		// id gets special treatment
		if(in_array('id', $this->getColumns()))
		{
			// hide the id by defaults
			$this->setColumnsHidden('id');

			// our JS needs to know an id, so we can highlight it
			$this->setRowAttributes(array('id' => 'row-[id]'));
		}

		// set default sorting options
		$this->setSortingOptions();

		// add classes on headers
		foreach($this->getColumns() as $column)
		{
			// set class
			$this->setColumnHeaderAttributes($column, array('class' => $column));

			// set default label
			$this->setHeaderLabels(array($column => SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($column)))));
		}

		// set paging class
		$this->setPagingClass('BackendDataGridPaging');

		// set default template
		$this->setTemplate(BACKEND_CORE_PATH . '/layout/templates/datagrid.tpl');
	}

	/**
	 * Adds a new column
	 *
	 * @param string $name The name for the new column.
	 * @param string[optional] $label The label for the column.
	 * @param string[optional] $value The value for the column.
	 * @param string[optional] $URL The URL for the link inside the column.
	 * @param string[optional] $title A title for the image inside the column.
	 * @param string[optional] $image An URL to the image inside the column.
	 * @param int[optional] $sequence The sequence for the column.
	 */
	public function addColumn($name, $label = null, $value = null, $URL = null, $title = null, $image = null, $sequence = null)
	{
		// known actions that should have a button
		if(in_array($name, array('add', 'edit', 'delete', 'details', 'approve', 'mark_as_spam', 'install')))
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
			$value = '<a href="' . $URL . '" class="button linkButton icon iconEdit icon' . SpoonFilter::toCamelCase($name) . '">
						<span>' . $value . '</span>
					</a>';

			// reset URL
			$URL = null;
		}

		// add the column
		parent::addColumn($name, $label, $value, $URL, $title, $image, $sequence);

		// known actions
		if(in_array($name, array('add', 'edit', 'delete', 'details', 'approve', 'mark_as_spam', 'install', 'use_revision', 'use_draft')))
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
	 * @param string $name The name for the new column.
	 * @param string[optional] $label The label for the column.
	 * @param string[optional] $value The value for the column.
	 * @param string[optional] $URL The URL for the link inside the column.
	 * @param string[optional] $title The title for the link inside the column.
	 * @param array[optional] $anchorAttributes The attributes for the anchor inside the column.
	 * @param string[optional] $image An URL to the image inside the column.
	 * @param int[optional] $sequence The sequence for the column.
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
		$this->setColumnAttributes($name, array(
			'class' => 'action action' . SpoonFilter::toCamelCase($name),
			'style' => 'width: 10%;'
		));

		// set header attributes
		$this->setColumnHeaderAttributes($name, array('class' => $name));
	}

	/**
	 * Enable the grey out functionallity. This will see if we have a column that matches our set.
	 * If so, it will call the BackendDatagridFunction with the type and value so we can parse the data.
	 */
	public function enableGreyingOut()
	{
		$allowedColumns = array('hidden', 'visible', 'active', 'published');
		$allColumns = $this->getColumns();

		foreach($allowedColumns as $column)
		{
			// we have a match, set the row function
			if(array_search($column, $allColumns) !== false)
			{
				$this->setColumnHidden($column);
				$this->setRowFunction(array('BackendDatagridFunctions', 'greyOut'), array($column, '[' . $column . ']'), array($column));
			}
		}
	}

	/**
	 * Enable drag and drop for the current datagrid
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
	 * @return string
	 */
	public function getContent()
	{
		// mass action was set
		if($this->tpl->getAssignedValue('massAction') !== null) $this->tpl->assign('footer', true);

		// has paging & more than 1 page
		elseif($this->getPaging() && $this->getNumResults() > $this->getPagingLimit()) $this->tpl->assign('footer', true);

		// set the odd and even classes
		$this->setOddRowAttributes(array('class' => 'odd'));
		$this->setEvenRowAttributes(array('class' => 'even'));

		// enable greying out
		$this->enableGreyingOut();

		// execute parent
		return parent::getContent();
	}

	/**
	 * Sets the active tab for this datagrid
	 *
	 * @param string $tab The name of the tab to show.
	 */
	public function setActiveTab($tab)
	{
		$this->setURL('#' . $tab, true);
	}

	/**
	 * Set a custom column confirm message.
	 *
	 * @param string $column The name of the column to set the confirm for.
	 * @param string $message The message to use as a confirmmessage.
	 * @param string[optional] $custom Unused parameter.
	 * @param string[optional] $title The title for the column.
	 * @param string[optional] $uniqueId A unique ID that will be uses.
	 */
	public function setColumnConfirm($column, $message, $custom = null, $title = null, $uniqueId = '[id]')
	{
		$column = (string) $column;
		$message = (string) $message;
		$custom = (string) $custom;
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
				if($title === null) $title = SpoonFilter::ucfirst(BL::lbl('Delete') . '?');

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
	 * @param mixed $function The function to execute.
	 * @param mixed[optional] $arguments The arguments to pass to the function.
	 * @param mixed $columns The column wherin the result will be printed.
	 * @param bool[optional] $overwrite Should the orginal value be overwritten.
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
	 * @param SpoonFormDropdown $actionDropDown A dropdown-instance.
	 */
	public function setMassAction(SpoonFormDropdown $actionDropDown)
	{
		// buid HTML
		$HTML = '<p><label for="' . $actionDropDown->getAttribute('id') . '">' . SpoonFilter::ucfirst(BL::lbl('WithSelected')) . '</label></p>
				<p>
					' . $actionDropDown->parse() . '
				</p>
				<div class="buttonHolder">
					<a href="#" class="submitButton button">
						<span>' . SpoonFilter::ucfirst(BL::lbl('Execute')) . '</span>
					</a>
				</div>';

		// assign parsed html
		$this->tpl->assign('massAction', $HTML);
	}

	/**
	 * Sets the checkboxes for the mass action
	 *
	 * @param string $column The name for the column that will hold the checkboxes.
	 * @param string $value The value for the checkbox.
	 * @param array[optional] $excludedValues The values that should be excluded.
	 * @param array[optional] $checkedValues The values that should be checked.
	 */
	public function setMassActionCheckboxes($column, $value, array $excludedValues = null, array $checkedValues = null)
	{
		// build label and value
		$label = '<span class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></span>';
		$value = '<input type="checkbox" name="id[]" value="' . $value . '" class="inputCheckbox" />';

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
	 * @param string $column The name of the column to set the tooltop for.
	 * @param string $message The key for the message (will be parsed through BL::msg).
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
	 * @param string $URL The URL to set.
	 * @param bool[optional] $append Should it be appended to the existing URL.
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
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendDataGridPaging implements iSpoonDataGridPaging
{
	/**
	 * Builds & returns the pagination
	 *
	 * @param string $URL
	 * @param int $offset
	 * @param string $order The name of the column to sort on.
	 * @param string $sort The sorting method, possible values are: asc, desc.
	 * @param int $numResults
	 * @param int $numPerPage The items per page.
	 * @param bool[optional] $debug
	 * @param string[optional] $compileDirectory
	 * @return string
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

		return $tpl->getContent(BACKEND_CORE_PATH . '/layout/templates/datagrid_paging.tpl');
	}
}

/**
 * A datagrid with an array as source
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendDataGridArray extends BackendDataGrid
{
	/**
	 * @param array $array The data.
	 */
	public function __construct(array $array)
	{
		$source = new SpoonDataGridSourceArray($array);
		parent::__construct($source);
	}
}

/**
 * A datagrid with a DB-connection as source
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendDataGridDB extends BackendDataGrid
{
	/**
	 * @param string $query The query to retrieve the data.
	 * @param array[optional] $parameters The parameters to be used inside the query.
	 * @param string[optional] $resultsQuery The optional count query, used to calculate the number of results.
	 * @param array[optional] $resultsParameters  Theh parameters to be used inside the results query.
	 */
	public function __construct($query, $parameters = array(), $resultsQuery = null, $resultsParameters = array())
	{
		// results query?
		$results = ($resultsQuery !== null) ? array($resultsQuery, $resultsParameters) : null;

		// create a new source-object
		$source = new SpoonDataGridSourceDB(BackendModel::getDB(), array($query, (array) $parameters), $results);

		parent::__construct($source);
	}
}

/**
 * A set of commonly used functions that will be applied on rows or columns
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendDataGridFunctions
{
	/**
	 * Formats plain text as HTML, links will be detected, paragraphs will be inserted
	 *
	 * @param string $var The data to cleanup.
	 * @return string
	 */
	public static function cleanupPlainText($var)
	{
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

		return $var;
	}

	/**
	 * Format a number as a float
	 *
	 * @param float $number The number to format.
	 * @param int[optional] $decimals The number of decimals.
	 * @return string
	 */
	public static function formatFloat($number, $decimals = 2)
	{
		$number = (float) $number;
		$decimals = (int) $decimals;

		return number_format($number, $decimals, '.', ' ');
	}

	/**
	 * Format a date according the users' settings
	 *
	 * @param int $timestamp The UNIX-timestamp to format as a human readable date.
	 * @return string
	 */
	public static function getDate($timestamp)
	{
		$timestamp = (int) $timestamp;

		// if invalid timestamp return an empty string
		if($timestamp <= 0) return '';

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('date_format');

		// format the date according the user his settings
		return SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage());
	}

	/**
	 * Format a date as a long representation according the users' settings
	 *
	 * @param int $timestamp The UNIX-timestamp to format as a human readable date.
	 * @return string
	 */
	public static function getLongDate($timestamp)
	{
		$timestamp = (int) $timestamp;

		// if invalid timestamp return an empty string
		if($timestamp <= 0) return '';

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('datetime_format');

		// format the date according the user his settings
		return SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage());
	}

	/**
	 * Format a time according the users' settings
	 *
	 * @param int $timestamp The UNIX-timestamp to format as a human readable time.
	 * @return string
	 */
	public static function getTime($timestamp)
	{
		$timestamp = (int) $timestamp;

		// if invalid timestamp return an empty string
		if($timestamp <= 0) return '';

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('time_format');

		// format the date according the user his settings
		return SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage());
	}

	/**
	 * Get time ago as a string for use in a datagrid
	 *
	 * @param int $timestamp The UNIX-timestamp to convert in a time-ago-string.
	 * @return string
	 */
	public static function getTimeAgo($timestamp)
	{
		$timestamp = (int) $timestamp;

		// get user setting for long dates
		$format = BackendAuthentication::getUser()->getSetting('datetime_format');

		// get the time ago as a string
		$timeAgo = SpoonDate::getTimeAgo($timestamp, BL::getInterfaceLanguage(), $format);

		return '<abbr title="' . SpoonDate::getDate($format, $timestamp, BL::getInterfaceLanguage()) . '">' . $timeAgo . '</abbr>';
	}

	/**
	 * Get the HTML for a user to use in a datagrid
	 *
	 * @param int $id The Id of the user.
	 * @return string
	 */
	public static function getUser($id)
	{
		$id = (int) $id;

		// create user instance
		$user = new BackendUser($id);

		// get settings
		$avatar = $user->getSetting('avatar', 'no-avatar.gif');
		$nickname = $user->getSetting('nickname');
		$allowed = BackendAuthentication::isAllowedAction('edit', 'users');

		// build html
		$html = '<div class="dataGridAvatar">' . "\n";
		$html .= '	<div class="avatar av24">' . "\n";
		if($allowed) $html .= '		<a href="' . BackendModel::createURLForAction('edit', 'users') . '&amp;id=' . $id . '">' . "\n";
		$html .= '			<img src="' . FRONTEND_FILES_URL . '/backend_users/avatars/32x32/' . $avatar . '" width="24" height="24" alt="' . $nickname . '" />' . "\n";
		if($allowed) $html .= '		</a>' . "\n";
		$html .= '	</div>';
		$html .= '	<p><a href="' . BackendModel::createURLForAction('edit', 'users') . '&amp;id=' . $id . '">' . $nickname . '</a></p>' . "\n";
		$html .= '</div>';

		return $html;
	}

	/**
	 * This will grey out certain rows from comon columns. These columns are:
	 *
	 * 'visible', 'hidden', 'active', 'published'
	 *
	 * @param string $type The type of column. This is given since some columns can have different meanings than others.
	 * @param string $value
	 * @param array $attributes
	 */
	public static function greyOut($type, $value, array $attributes = array())
	{
		// the base class
		$grayedOutClass = 'grayedOut';

		switch($type)
		{
			case 'visible':
			case 'active':
			case 'published':
				if($value == 'N') return array('class' => $grayedOutClass);
				break;
			case 'hidden':
				if($value == 'Y') return array('class' => $grayedOutClass);
				break;
		}

		return array();
	}

	/**
	 * Returns an image tag
	 *
	 * @param string $path The path to the image.
	 * @param string $image The filename of the image.
	 * @param string[optional] $title The title (will be used as alt).
	 * @return string
	 */
	public static function showImage($path, $image, $title = '')
	{
		$path = (string) $path;
		$image = (string) $image;
		$title = (string) $title;

		return '<img src="' . $path . '/' . $image . '" alt="' . $title . '" />';
	}

	/**
	 * Truncate a string
	 *
	 * @param string[optional] $string The string to truncate.
	 * @param int $length The maximumlength for the string.
	 * @param bool[optional] $useHellip Should a hellip be appended?
	 * @return string
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

			return SpoonFilter::htmlspecialchars($string);
		}
	}
}
