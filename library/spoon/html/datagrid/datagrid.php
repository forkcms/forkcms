<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			html
 * @subpackage		datagrid
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonDataGridException class */
require_once 'spoon/html/datagrid/exception.php';

/** SpoonDataGridColumn class */
require_once 'spoon/html/datagrid/column.php';

/** SpoonDataGridSourceDB class */
require_once 'spoon/html/datagrid/source.php';

/** SpoonDataGridPaging class */
require_once 'spoon/html/datagrid/paging.php';

/** SpoonTemplate class */
require_once 'spoon/template/template.php';


/**
 * This class is the base class used to generate datagrids
 * from database queries.
 *
 * @package			html
 * @subpackage		datagrid
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
class SpoonDataGrid
{
	/**
	 * List of columns that may be sorted in, based on the query results
	 *
	 * @var	array
	 */
	private $allowedSortingColumns = array();


	/**
	 * Html attributes
	 *
	 * @var	array
	 */
	private $attributes = array('datagrid' => array(),
								'header' => array(),
								'row' => array(),
								'row_even' => array(),
								'row_odd' => array(),
								'footer' => array());


	/**
	 * Table caption/description
	 *
	 * @var	string
	 */
	private $caption;


	/**
	 * Array of column functions
	 *
	 * @var	array
	 */
	private $columnFunctions = array();


	/**
	 * Array of column objects
	 *
	 * @var	array
	 */
	private $columns = array();


	/**
	 * Default compile directory
	 *
	 * @var	string
	 */
	private $compileDirectory;


	/**
	 * Final output
	 *
	 * @var	string
	 */
	private $content;


	/**
	 * Debug status
	 *
	 * @var	bool
	 */
	private $debug = false;


	/**
	 * Offset value
	 *
	 * @var	int
	 */
	private $offset;


	/**
	 * Offset start value
	 *
	 * @var	int
	 */
	private $offsetParameter;


	/**
	 * Order start value
	 *
	 * @var	string
	 */
	private $orderParameter;


	/**
	 * Separate results with pages
	 *
	 * @var	bool
	 */
	private $paging = true;


	/**
	 * Default number of results per page
	 *
	 * @var	int
	 */
	private $pagingLimit = 30;


	/**
	 * Class used to define paging
	 *
	 * @var	SpoonDataGridPaging
	 */
	private $pagingClass = 'SpoonDataGridPaging';


	/**
	 * Parse status
	 *
	 * @var	bool
	 */
	private $parsed = false;


	/**
	 * Default sorting column
	 *
	 * @var	string
	 */
	private $sortingColumn;


	/**
	 * Sorting columns (cached when requested)
	 *
	 * @var array
	 */
	private $sortingColumns = array();


	/**
	 * Sorting icons
	 *
	 * @var	array
	 */
	private $sortingIcons = array(	'asc' => null,
									'ascSelected' => null,
									'desc' => null,
									'descSelected' => null);


	/**
	 * Sorting Labels
	 *
	 * @var	array
	 */
	private $sortingLabels = array(	'asc' => 'Sort ascending',
									'ascSelected' => 'Sorted ascending',
									'desc' => 'Sort descending',
									'descSelected' => 'Sorted descending');


	/**
	 * Default sorting method
	 *
	 * @var	string
	 */
	private $sortParameter;


	/**
	 * Source of the datagrid
	 *
	 * @var	SpoonDataGridSource
	 */
	private $source;


	/**
	 * Datagrid summary
	 *
	 * @var	string
	 */
	private $summary;


	/**
	 * Default or custom template
	 *
	 * @var	string
	 */
	private $template;


	/**
	 * Template instance
	 *
	 * @var	SpoonTemplate
	 */
	private $tpl;


	/**
	 * Basic url
	 *
	 * @var	string
	 */
	private $url;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	SpoonDataGridSource $source
	 * @param	string[optional] $template
	 */
	public function __construct(SpoonDataGridSource $source, $template = null)
	{
		// set source
		$this->setSource($source);

		// set template
		if($template !== null) $this->setTemplate($template);

		// create default columns
		$this->createColumns();
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
		// redefine name
		$name = (string) $name;

		// column already exists
		if(isset($this->columns[$name])) throw new SpoonDataGridException('A column with the name "'. $name .'" already exists.');

		// redefine sequence
		if($sequence === null) $sequence = count($this->columns) + 1;

		// new column
		$this->columns[$name] = new SpoonDataGridColumn($name, $label, $value, $url, $title, $image, $sequence);
	}


	/**
	 * Builds the requested url
	 *
	 * @return	string
	 * @param	int $offset
	 * @param	string $order
	 * @param	string $sort
	 */
	private function buildURL($offset, $order, $sort)
	{
		return str_replace(array('[offset]', '[order]', '[sort]'), array($offset, $order, $sort), $this->url);
	}


	/**
	 * Clears the attributes
	 *
	 * @return	void
	 */
	public function clearAttributes()
	{
		$this->attributes['datagrid'] = array();
	}


	/**
	 * Clears the attributes for a specific column
	 *
	 * @return	void
	 * @param	string $column
	 */
	public function clearColumnAttributes($column)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// column doesn't exist
			if(!isset($this->columns[(string) $column])) throw new SpoonDataGridException('The column "'. (string) $column .'" doesn\'t exist and therefor no attributes can be removed.');

			// exists
			$this->columns[(string) $column]->clearAttributes();
		}
	}


	/**
	 * Clears the even row attributes
	 *
	 * @return	void
	 */
	public function clearEvenRowAttributes()
	{
		$this->attributes['row_even'] = array();
	}


	/**
	 * clears the odd row attributes
	 *
	 * @return	void
	 */
	public function clearOddRowAttributes()
	{
		$this->attributes['row_odd'] = array();
	}


	/**
	 * Clears the row attributes
	 *
	 * @return	void
	 */
	public function clearRowAttributes()
	{
		$this->attributes['row'] = array();
	}


	/**
	 * Creates the default columns, based on the query
	 *
	 * @return	void
	 */
	private function createColumns()
	{
		// has results
		if($this->source->getNumResults() != 0)
		{
			// fetch the column names
			foreach($this->source->getColumns() as $column)
			{
				// add column
				$this->addColumn($column, $column, '['. $column .']', null, null, null, (count($this->columns) +1));

				// may be sorted on
				$this->allowedSortingColumns[] = $column;
			}
		}
	}


	/**
	 * Shows the output & stops script execution
	 *
	 * @return	void
	 */
	public function display()
	{
		echo $this->getContent();
		exit;
	}


	/**
	 * Generates the array with the order columns
	 *
	 * @return	void
	 */
	private function generateOrder()
	{
		// delete current cache of sortable columns
		$this->sortingColumns = array();

		// columns present
		if(count($this->columns) != 0)
		{
			// loop columns
			foreach($this->columns as $column)
			{
				// allowed sorting, sorting enabled specifically
				if(in_array($column->getName(), $this->allowedSortingColumns) && $column->getSorting())
				{
					// add to the list
					$this->sortingColumns[] = $column->getName();

					// default
					$default = $column->getName();
				}
			}

			// no default defined
			if($this->sortingColumn === null && isset($default)) $this->sortingColumn = $default;
		}
	}


	/**
	 * Retrieve all the datagrid attributes
	 *
	 * @return	array
	 */
	public function getAttributes()
	{
		return $this->attributes['datagrid'];
	}


	/**
	 * Retrieve the columns sequence
	 *
	 * @return	array
	 */
	private function getColumnsSequence()
	{
		// loop all the columns
		foreach($this->columns as $column) $aSequence[$column->getSequence()] = $column->getName();

		// reindex
		return SpoonFilter::arraySortKeys($aSequence);
	}


	/**
	 * Retrieve the final output
	 *
	 * @return	string
	 */
	public function getContent()
	{
		// parse if needed
		if(!$this->parsed) $this->parse();

		// fetch content
		return $this->content;
	}


	/**
	 * Fetch the debug status
	 *
	 * @return	bool
	 */
	public function getDebug()
	{
		return $this->debug;
	}


	/**
	 * Returns the html attributes based on an array
	 *
	 * @return	string
	 * @param	array[optional] $array
	 */
	private function getHtmlAttributes(array $array = array())
	{
		// output
		$html = '';

		// loop elements
		foreach($array as $label => $value) $html .= ' '. $label .'="'. $value .'"';
		return $html;
	}


	/**
	 * Retrieve the offset value
	 *
	 * @return	int
	 */
	public function getOffset()
	{
		// default offset
		$offset = null;

		// paging enabled
		if($this->paging)
		{
			// has results
			if($this->source->getNumResults() != 0)
			{
				// offset parameter defined
				if($this->offsetParameter !== null) $offset = $this->offsetParameter;

				// use default
				else $offset = (isset($_GET['offset'])) ? (int) $_GET['offset'] : 0;

				// offset cant be bigger than the number of results
				if($offset >= $this->source->getNumResults()) $offset = (int) $this->source->getNumResults() - $this->pagingLimit;

				// offset divided by the per page limit should have no rest
				if(($offset % $this->pagingLimit) != 0) $offset = 0;

				// offset minus the pagina limit may not go below zero
				if(($offset - $this->pagingLimit) < 0) $offset = 0;
			}

			// no results
			else $offset = 0;
		}

		return $offset;
	}


	/**
	 * Retrieves the column that's currently being sorted on
	 *
	 * @return	string
	 */
	public function getOrder()
	{
		// default value
		$order = null;

		// sorting enabled
		if($this->getSorting())
		{
			/**
			 * First the list of columns that can be ordered on,
			 * must be re-generated
			 */
			$this->generateOrder();

			// order parameter defined
			if($this->orderParameter !== null) $order = $this->orderParameter;

			// defaut order
			else $order = (isset($_GET['order'])) ? (string) $_GET['order'] : null;

			// retrieve order
			$order = SpoonFilter::getValue($order, $this->sortingColumns, $this->sortingColumn);
		}

		return $order;
	}


	/**
	 * Retrieve the number of results for this datagrids' source
	 *
	 * @return	int
	 */
	public function getNumResults()
	{
		return $this->source->getNumResults();
	}


	/**
	 * Paging status
	 *
	 * @return	bool
	 */
	public function getPaging()
	{
		return $this->paging;
	}


	/**
	 * Fetch the paging class
	 *
	 * @return	string
	 */
	public function getPagingClass()
	{
		return $this->pagingClass;
	}


	/**
	 * Fetch the number of items per page
	 *
	 * @return	int
	 */
	public function getPagingLimit()
	{
		return ($this->paging) ? $this->pagingLimit : null;
	}


	/**
	 * Retrieve the sorting method
	 *
	 * @return	string
	 */
	public function getSort()
	{
		// sort parameter defined
		if($this->sortParameter !== null) $sort = $this->sortParameter;

		// default sort
		else $sort = (isset($_GET['sort'])) ? (string) $_GET['sort'] : null;

		// retrieve sort
		$sort = SpoonFilter::getValue($sort, array('asc', 'desc'), 'asc');

		return $sort;
	}


	/**
	 * Retrieve the sorting status
	 *
	 * @return	bool
	 */
	public function getSorting()
	{
		// generate order
		$this->generateOrder();

		// sorting columns exist?
		return (count($this->sortingColumns) != 0) ? true : false;
	}


	/**
	 * Retrieve the full template path
	 *
	 * @return	string
	 */
	private function getTemplatePath()
	{
		// template was provided
		if($this->template !== null) $path = $this->template;

		// default template
		else
		{
			// get info
			$aInfo = SpoonFile::getFileInfo(__FILE__);

			// path
			$path = $aInfo['path'] .'/datagrid.tpl';
		}

		return $path;
	}


	/**
	 * Parse the final output
	 *
	 * @return	void
	 */
	private function parse()
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// fetch records
			$aRecords = $this->source->getData($this->getOffset(), $this->getPagingLimit(), $this->getOrder(), $this->getSort());

			// has results
			if(count($aRecords) != 0)
			{
				// load template
				$this->tpl = new SpoonTemplate();

				// compile directory
				$compileDirectory = ($this->compileDirectory !== null) ? $this->compileDirectory : dirname(realpath(__FILE__));
				$this->tpl->setCompileDirectory($compileDirectory);

				// only force compiling when debug is enabled
				if($this->debug) $this->tpl->setForceCompile(true);

				// table attributes
				$this->parseAttributes();

				// table summary
				$this->parseSummary();

				// caption/description
				$this->parseCaption();

				// header row
				$this->parseHeader();

				// actual rows
				$this->parseBody($aRecords);

				// pagination
				$this->parseFooter();

				// parse to buffer
				ob_start();
				$this->tpl->display($this->getTemplatePath());
				$this->content = ob_get_clean();
			}
		}

		// update parsed status
		$this->parsed = true;
	}


	/**
	 * Parses the datagrid attributes
	 *
	 * @return	void
	 */
	private function parseAttributes()
	{
		$this->tpl->assign('attributes', $this->getHtmlAttributes($this->attributes['datagrid']));
	}


	/**
	 * Parses the body
	 *
	 * @return	void
	 * @param	array $records
	 */
	private function parseBody(array $records)
	{
		// init var
		$aRows = array();

		// columns sequence
		$aSequence = $this->getColumnsSequence();

		// loop records
		foreach($records as $i => &$record)
		{
			// special record
			$record = $this->parseRecord($record);

			// parse column functions
			$record = $this->parseColumnFunctions($record);

			// reset row
			$aRow = array('attributes' => '', 'oddAttributes' => '', 'evenAttributes' => '', 'columns' => array());

			// row attributes
			$aRow['attributes'] = str_replace($record['labels'], $record['values'], $this->getHtmlAttributes($this->attributes['row']));

			// odd row attributes (reversed since the first $i = 0)
			if(!SpoonFilter::isOdd($i)) $aRow['oddAttributes'] = str_replace($record['labels'], $record['values'], $this->getHtmlAttributes($this->attributes['row_odd']));

			// even row attributes
			else $aRow['evenAttributes'] = str_replace($record['labels'], $record['values'], $this->getHtmlAttributes($this->attributes['row_even']));

			// define the columns
			$aColumns = array();

			// loop columns
			foreach($aSequence as $name)
			{
				// column
				$column = $this->columns[$name];

				// has overwrite enabled
				if($column->getOverwrite())
				{
					// fetch key
					$iKey = array_search('['. $column->getName() .']', $record['labels']);

					// parse the actual value
					$columnValue = $record['values'][$iKey];
				}

				// no overwrite status
				else
				{
					// default value
					$columnValue = '';

					// has an url
					if($column->getUrl() !== null)
					{
						// open url tag
						$columnValue .= '<a href="'. str_replace($record['labels'], $record['values'], $column->getUrl()) .'"';

						// add title
						$columnValue .= ' title="'. str_replace($record['labels'], $record['values'], $column->getUrlTitle()) .'"';

						// confirm
						if($column->getConfirm() && $column->getConfirmMessage() !== null)
						{
							// default confirm
							if($column->getConfirmCustom() == '') $columnValue .= ' onclick="return confirm(\''. str_replace($record['labels'], $record['values'], $column->getConfirmMessage()) .'\');"';

							// custom confirm
							else
							{
								// replace the message
								$tmpValue = str_replace('%message%', $column->getConfirmMessage(), $column->getConfirmCustom());

								// make vars available
								$tmpValue = str_replace($record['labels'], $record['values'], $tmpValue);

								// add id
								$columnValue .= ' '. $tmpValue;
							}
						}

						// close start tag
						$columnValue .= '>';
					}

					// has an image
					if($column->getImage() !== null)
					{
						// open img tag
						$columnValue .= '<img src="'. str_replace($record['labels'], $record['values'], $column->getImage()) .'"';

						// add title & alt
						$columnValue .= ' alt="'. str_replace($record['labels'], $record['values'], $column->getImageTitle()) .'"';
						$columnValue .= ' title="'. str_replace($record['labels'], $record['values'], $column->getImageTitle()) .'"';

						// close img tag
						$columnValue .= ' />';
					}

					// regular value
					else
					{
						// fetch key
						$iKey = array_search('['. $column->getName() .']', $record['labels']);

						// parse value
						$columnValue .= $record['values'][$iKey];
					}

					// has an url (close the tag)
					if($column->getUrl() !== null) $columnValue .= '</a>';
				}

				// fetch column attributes
				$columnAttributes = $this->getHtmlAttributes($column->getAttributes());

				// visible & iteration
				if(!$column->getHidden())
				{
					// add this column
					$aColumns[] = array('attributes' => $columnAttributes, 'value' => $columnValue);

					// add to custom list
					$aRow['column'][$name] = $columnValue;
				}
			}

			// add the columns to the rows
			$aRow['columns'] = $aColumns;

			// add the row
			$aRows[] = $aRow;
		}

		// assign body
		$this->tpl->assign('rows', $aRows);

		// assign the number of columns
		$this->tpl->assign('numColumns', count($aRows[0]['columns']));
	}


	/**
	 * Parses the caption tag
	 *
	 * @return	void
	 */
	private function parseCaption()
	{
		if($this->caption !== null) $this->tpl->assign('caption', $this->caption);
	}


	/**
	 * Parses the column functions
	 *
	 * @return	array
	 * @param	array $record
	 */
	private function parseColumnFunctions($record)
	{
		// loop functions
		foreach ($this->columnFunctions as $function)
		{
			// no arguments given
			if(is_null($function['arguments'])) $value = @call_user_func($function['function']);

			// array
			elseif(is_array($function['arguments']))
			{
				// replace arguments
				$function['arguments'] = str_replace($record['labels'], $record['values'], $function['arguments']);

				// execute function
				$value = @call_user_func_array($function['function'], $function['arguments']);
			}

			// no null/array
			else $value = @call_user_func($function['function'], str_replace($record['labels'], $record['values'], $function['arguments']));

			/**
			 * Now that we have the return value of this method, we can
			 * do the actual writeback to the column(s). If overwrite was
			 * true, we're going to enable the overwrite of the writeback column(s)
			 */

			// one column, that exists
			if(is_string($function['columns']) && isset($this->columns[$function['columns']]))
			{
				// fetch key
				$iKey = array_search('['. $function['columns'] .']', $record['labels']);

				// value was set
				if($iKey !== false)
				{
					// update value
					$record['values'][$iKey] = $value;

					// update overwrite
					if($function['overwrite']) $this->columns[$function['columns']]->setOverwrite(true);
				}
			}

			// write to multiple columns
			elseif(is_array($function['columns']) && count($function['columns']) != 0)
			{
				// loop target columns
				foreach($function['columns'] as $column)
				{
					// fetch key
					$iKey = array_search('['. $column .']', $record['labels']);

					// value was set
					if($iKey !== false)
					{
						// update value
						$record['values'][$iKey] = $value;

						// update overwrite
						if($function['overwrite']) $this->columns[$column]->setOverwrite(true);
					}
				}
			}
		}

		return $record;
	}


	/**
	 * Parses the footer
	 *
	 * @return	void
	 */
	private function parseFooter()
	{
		// attributes
		$this->tpl->assign('footer', array('attributes' => $this->getHtmlAttributes($this->attributes['footer'])));

		// parse paging
		$this->parsePaging();
	}


	/**
	 * Parses the header row
	 *
	 * @return	void
	 */
	private function parseHeader()
	{
		// init vars
		$aHeader = array();

		// attributes
		$this->tpl->assign('headerAttributes', $this->getHtmlAttributes($this->attributes['header']));

		// sequence
		$aSequence = $this->getColumnsSequence();

		// sorting enabled?
		$sorting = $this->getSorting();

		// sortable columns
		$aSortingColumns = array();
		foreach($aSequence as $column) if($this->columns[$column]->getSorting()) $aSortingColumns[] = $column;

		// loop columns
		foreach($aSequence as $name)
		{
			// define column
			$aColumn = array();

			// column
			$column = $this->columns[$name];

			// visible
			if(!$column->getHidden())
			{
				// sorting globally enabled AND for this column
				if($sorting && in_array($name, $aSortingColumns))
				{
					// init var
					$aColumn['sorting'] = true;
					$aColumn['noSorting'] = false;

					// sorted on this column?
					if($this->getOrder() == $name)
					{
						// sorted
						$aColumn['sorted'] = true;
						$aColumn['notSorted'] = false;

						// asc
						if($this->getSort() == 'asc')
						{
							$aColumn['sortedAsc'] = true;
							$aColumn['sortedDesc'] = false;
						}

						// desc
						else
						{
							$aColumn['sortedAsc'] = false;
							$aColumn['sortedDesc'] = true;
						}
					}

					/**
					 * This column is sortable, but there's currently not being
					 * sorted on this column
					 */
					elseif(in_array($name, $aSortingColumns))
					{
						$aColumn['sorted'] = false;
						$aColumn['notSorted'] = true;
					}

					/**
					 * URL's are parsed for the opposite column, as for the asc & desc version
					 * for this column. If the sorting is currently not on this column
					 * the default sorting method (mostly asc) will be used to define the opposite/default
					 * sorting method.
					 */

					// currently not sorting on this column
					if($this->getOrder() != $name) $sortingMethod = $this->columns[$name]->getSortingMethod();

					// sorted on this column ascending
					elseif($this->getSort() == 'asc') $sortingMethod = 'desc';

					// sorting on this column descending
					else $sortingMethod = 'asc';

					// build actual urls
					$aColumn['sortingURL'] = $this->buildURL($this->getOffset(), $name, $sortingMethod);
					$aColumn['sortingURLAsc'] = $this->buildURL($this->getOffset(), $name, 'asc');
					$aColumn['sortingURLDesc'] = $this->buildURL($this->getOffset(), $name, 'desc');

					/**
					 * There's no point in parsing the icon for this column if there's
					 * not being sorted on this column.
					 */

					/**
					 * To define the default icon for sorting, we need to apply
					 * the same rules as with the default url. See those comments for
					 * the necessary details.
					 */
					if($this->getOrder() != $name) $sortingIcon = $this->sortingIcons[$this->columns[$name]->getSortingMethod()];

					// sorted on this column asc/desc
					elseif($this->getSort() == 'asc') $sortingIcon = $this->sortingIcons['ascSelected'];
					else $sortingIcon = $this->sortingIcons['descSelected'];

					// asc & desc icons
					$aColumn['sortingIcon'] = $sortingIcon;
					$aColumn['sortingIconAsc'] = ($this->getSort() == 'asc') ? $this->sortingIcons['ascSelected'] : $this->sortingIcons['asc'];
					$aColumn['sortingIconDesc'] = ($this->getSort() == 'desc') ? $this->sortingIcons['descSelected'] : $this->sortingIcons['desc'];

					// not sorted on this column
					if($this->getOrder() != $name) $sortingLabel = $this->sortingLabels[$this->columns[$name]->getSortingMethod()];

					// sorted on this column asc/desc
					elseif($this->getSort() == 'asc') $sortingLabel = $this->sortingLabels['ascSelected'];
					else $sortingLabel = $this->sortingLabels['descSelected'];

					$aColumn['sortingLabel'] = $sortingLabel;
					$aColumn['sortingLabelAsc'] = $this->sortingLabels['asc'];
					$aColumn['sortingLabelDesc'] = $this->sortingLabels['desc'];
				}

				// no sorting enabled for this column
				else
				{
					$aColumn['sorting'] = false;
					$aColumn['noSorting'] = true;
				}

				// parse vars
				$aColumn['label'] = $column->getLabel();

				// add to array
				$aHeader[] = $aColumn;
			}
		}

		// default headers
		$this->tpl->assign('headers', $aHeader);
	}


	/**
	 * Parses the paging
	 *
	 * @return	void
	 */
	private function parsePaging()
	{
		// enabled
		if($this->paging)
		{
			// offset, order & sort
			$this->tpl->assign(array('offset', 'order', 'sort'), array($this->getOffset(), $this->getOrder(), $this->getSort()));

			// number of results
			$this->tpl->assign('iResults', $this->source->getNumResults());

			// number of pages
			$this->tpl->assign('iPages', ceil($this->source->getNumResults() / $this->pagingLimit));

			// current page
			$this->tpl->assign('iCurrentPage', ceil($this->getOffset() / $this->pagingLimit) + 1);

			// number of items per page
			$this->tpl->assign('iPerPage', $this->pagingLimit);

			// parse paging
			$content = SpoonDataGridPaging::getContent($this->url, $this->getOffset(), $this->getOrder(), $this->getSort(), $this->source->getNumResults(), $this->pagingLimit, $this->debug, $this->compileDirectory);
			$this->tpl->assign('paging', $content);
		}
	}


	/**
	 * Parses the record
	 *
	 * @return	array
	 * @param	array $record
	 */
	private function parseRecord(array $record)
	{
		// create labels/values array
		foreach($record as $label => $value)
		{
			$array['labels'][] = '['. $label .']';
			$array['values'][] = $value;
		}

		// add offset?
		if($this->paging)
		{
			$array['labels'][] = '[offset]';
			$array['values'][] = $this->getOffset();
		}

		// sorting
		if(count($this->sortingColumns) != 0)
		{
			$array['labels'][] = '[order]';
			$array['labels'][] = '[sort]';
			// --
			$array['values'][] = $this->getOrder();
			$array['values'][] = $this->getSort();
		}

		// loop the record fields
		foreach($this->columns as $column)
		{
			// this column is an extra field, added in the datagrid
			if(!in_array('['. $column->getName() .']', $array['labels']))
			{

				$array['values'][] = str_replace($array['labels'], $array['values'], $column->getValue());
				$array['labels'][] = '['. $column->getName() .']';
			}
		}

		return $array;
	}


	/**
	 * Parses the summary
	 *
	 * @return	void
	 */
	private function parseSummary()
	{
		if($this->summary !== null) $this->tpl->assign('summary', $this->summary);
	}


	/**
	 * Set main datagrid attributes
	 *
	 * @return	void
	 * @param	array $attributes
	 */
	public function setAttributes(array $attributes)
	{
		foreach($attributes as $key => $value) $this->attributes['datagrid'][(string) $key] = (string) $value;
	}


	/**
	 * Sets the table caption or main description
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setCaption($value)
	{
		$this->caption = (string) $value;
	}


	/**
	 * Set one or more attributes for a column
	 *
	 * @return	void
	 * @param	string $column
	 * @param	array $attributes
	 */
	public function setColumnAttributes($column, array $attributes)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// column doesnt exist
			if(!isset($this->columns[$column])) throw new SpoonDataGridException('The column "'. $column .'" doesn\'t exist, therefor no attributes can be added.');

			// exists
			else $this->columns[$column]->setAttributes($attributes);
		}
	}


	/**
	 * Set a custom column confirm message
	 *
	 * @return	void
	 * @param	string $column
	 * @param	string $message
	 * @param	string[optional] $custom
	 */
	public function setColumnConfirm($column, $message, $custom = null)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// column doesnt exist
			if(!isset($this->columns[$column])) throw new SpoonDataGridException('The column "'. $column .'" doesn\'t exist, therefor no confirm message/script can be added.');

			// exists
			else $this->columns[$column]->setConfirm($message, $custom);
		}
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
	public function setColumnFunction($function, $arguments = null, $columns, $overwrite = false)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// regular function
			if(!is_array($function))
			{
				// function checks
				if(!function_exists((string) $function)) throw new SpoonDataGridException('The function "'. (string) $function .'" doesn\'t exist.');
			}

			// class method
			else
			{
				// method checks
				if(count($function) != 2) throw new SpoonDataGridException('When providing a method for a column function is must be like array(\'class\', \'method\')');

				// method doesn't exist
				elseif(!method_exists($function[0], $function[1])) throw new SpoonDataGridException('The method '. (string) $function[0] .'::'. (string) $function[1] .' does not exist.');
			}

			// add to function stack
			$this->columnFunctions[] = array('function' => $function, 'arguments' => $arguments, 'columns' => $columns, 'overwrite' => (bool) $overwrite);
		}
	}


	/**
	 * Sets a single column hidden
	 *
	 * @return	void
	 * @param	string $column
	 * @param	bool[optional] $on
	 */
	public function setColumnHidden($column, $on = true)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// column doesn't exist
			if(!isset($this->columns[(string) $column])) throw new SpoonDataGridException('The column "'. (string) $column .'" doesn\'t exist and therefor can\'t be set hidden.');

			// exists
			$this->columns[(string) $column]->setHidden($on);
		}
	}


	/**
	 * Sets one or more columns hidden
	 *
	 * @return	void
	 * @param	array $columns
	 */
	public function setColumnsHidden($columns)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// array
			if(is_array($columns)) foreach($columns as $column) $this->setColumnHidden($column);

			// multiple arguments
			else
			{
				// redefine columns
				$columns = func_get_args();

				// set columns hidden
				foreach($columns as $column) $this->setColumnHidden($column);
			}
		}
	}


	/**
	 * Sets the columns sequence
	 *
	 * @return	void
	 */
	public function setColumnsSequence($columns)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// array
			if(is_array($columns)) call_user_method_array('setColumnsSequence', $this, $columns);

			// multiple arguments
			else
			{
				// current sequence
				$aSequence = $this->getColumnsSequence();

				// fetch arguments
				$arguments = func_get_args();

				// build columns
				$aColumns = (is_array($arguments[0])) ? $arguments[0] : $arguments;

				// counter
				$i = 1;

				// loop colums
				foreach ($aColumns as $column)
				{
					// column exists
					if(!isset($this->columns[(string) $column])) throw new SpoonDataGridException('The column "'. (string) $column .'" doesn\'t exist. Therefor its sequence can\'t be altered.');

					// update sequence
					$this->columns[(string) $column]->setSequence($i);

					// remove from the original list
					$iKey = (int) array_search((string) $column, $aSequence);
					unset($aSequence[$iKey]);

					// update counter
					$i++;
				}

				// reset counter
				$i = 1;

				// add remaining columns
				foreach($aSequence as $sequence)
				{
					// update sequence
					$this->columns[$sequence]->setSequence(count($aColumns) + $i);

					// update counter
					$i++;
				}
			}
		}
	}


	/**
	 * Set the default sorting method for a column
	 *
	 * @return	void
	 * @param	string $column
	 * @param	string[optional] $sort
	 */
	public function setColumnSortingMethod($column, $sort = 'asc')
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// column doesn't exist
			if(!isset($this->columns[(string) $column])) throw new SpoonDataGridException('The column "'. (string) $column .'" doesn\'t exist and therefor no default sorting method can be applied.');

			// exists
			$this->columns[(string) $column]->setSortingMethod($sort);
		}
	}


	/**
	 * Set the url for a column
	 *
	 * @return	void
	 * @param	string $column
	 * @param	string $url
	 * @param	string[optional] $title
	 */
	public function setColumnUrl($column, $url, $title = null)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// column doesn't exist
			if(!isset($this->columns[(string) $column])) throw new SpoonDataGridException('The column "'. (string) $column .'" doesn\'t exist and therefor no url can be applied.');

			// exists
			$this->columns[(string) $column]->setUrl($url, $title);
		}
	}


	/**
	 * Sets the compile directory
	 *
	 * @return	void
	 * @param	string $path
	 */
	public function setCompileDirectory($path)
	{
		$this->compileDirectory = (string) $path;
	}


	/**
	 * Adjust the debug setting
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setDebug($on = true)
	{
		$this->debug = (bool) $on;
	}


	/**
	 * Set the even row attributes
	 *
	 * @return	void
	 * @param	array $attributes
	 */
	public function setEvenRowAttributes(array $attributes)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// add to the list
			foreach($attributes as $key => $value) $this->attributes['row_even'][(string) $key] = (string) $value;
		}
	}


	/**
	 * Set some custom header attributes
	 *
	 * @return	void
	 * @param	array $attributes
	 */
	public function setHeaderAttributes(array $attributes)
	{
		foreach($attributes as $key => $value) $this->attributes['header'][(string) $key] = (string) $value;
	}


	/**
	 * Set the header labels
	 *
	 * @return	void
	 * @param	array $labels
	 */
	public function setHeaderLabels(array $labels)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// loop the keys
			foreach($labels as $column => $label)
			{
				// column doesn't exist
				if(!isset($this->columns[$column])) throw new SpoonDataGridException('The column "'. $column .'" doesn\t exist, therefor no label can be assigned.');

				// exists
				else $this->columns[$column]->setLabel($label);
			}
		}
	}


	/**
	 * Set the odd row attributes
	 *
	 * @return	void
	 * @param	array $attributes
	 */
	public function setOddRowAttributes(array $attributes)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// add to the list
			foreach($attributes as $key => $value) $this->attributes['row_odd'][(string) $key] = (string) $value;
		}
	}


	/**
	 * Sets the value for offset. eg from the url
	 *
	 * @return	void
	 * @param	int[optional] $value
	 */
	public function setOffsetParameter($value = null)
	{
		$this->offsetParameter = (int) $value;
	}


	/**
	 * Sets the value for the order. eg from the url
	 *
	 * @return	void
	 * @param	string[optional] $value
	 */
	public function setOrderParameter($value = null)
	{
		$this->orderParameter = (string) $value;
	}


	/**
	 * Allow/disallow showing the results on multiple pages
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setPaging($on = false)
	{
		$this->paging = (bool) $on;
	}


	/**
	 * Sets the alternative paging class
	 *
	 * @return	void
	 */
	public function setPagingClass($class)
	{
		// class cant be found
		if(!class_exists((string) $class)) throw new SpoonDataGridException('The class "'. (string) $class .'" you provided for the alternative paging can not be found.');

		// does not extend SpoonDataGridPaging
		if(!is_subclass_of($class, 'SpoonDataGridPaging')) throw new SpoonDataGridException('The class "'. (string) $class .'" does not extend SpoonDataGridPaging which is obligated.');

		// set the class
		else $this->pagingClass = $class;
	}


	/**
	 * Sets the number of results per page
	 *
	 * @return	void
	 * @param	int[optional] $limit
	 */
	public function setPagingLimit($limit = 30)
	{
		$this->pagingLimit = abs((int) $limit);
	}


	/**
	 * Sets the row attributes
	 *
	 * @return	void
	 * @param	array $attributes
	 */
	public function setRowAttributes(array $attributes)
	{
		foreach($attributes as $key => $value) $this->attributes['row'][(string) $key] = (string) $value;
	}


	/**
	 * Sets the columns that may be sorted on
	 *
	 * @return	void
	 * @param	array $columns
	 * @param	string[optional] $default
	 */
	public function setSortingColumns(array $columns, $default = null)
	{
		// has results
		if($this->source->getNumResults() > 0)
		{
			// loop columns
			foreach($columns as $column)
			{
				// column doesn't exist
				if(!isset($this->columns[(string) $column])) throw new SpoonDataGridException('The column "'. (string) $column .'" doesn\'t exist and therefor can\'t be sorted on.');

				// column exists
				else
				{
					// not sortable
					if(!in_array((string) $column, $this->allowedSortingColumns)) throw new SpoonDataGridException('The column "'. (string) $column .'" can\'t be sorted on.');

					// sortable
					else
					{
						// enable sorting
						$this->columns[(string) $column]->setSorting(true);

						// set default sorting
						if(!isset($defaultColumn)) $defaultColumn = (string) $column;
					}
				}
			}

			// default column set
			if($default !== null && in_array($defaultColumn, $columns)) $defaultColumn = (string) $default;

			// default column is not good
			if(!in_array($defaultColumn, $this->allowedSortingColumns)) throw new SpoonDataGridException('The column "'. $defaultColumn .'" can\'t be set as the default sorting column, because it doesn\'t exist or may not be sorted on.');

			// set default column
			$this->sortingColumn = $defaultColumn;
		}
	}


	/**
	 * Sets the sorting icons
	 *
	 * @return	void
	 * @param	string[optional] $asc
	 * @param	string[optional] $ascSelected
	 * @param	string[optional] $desc
	 * @param	string[optional] $descSelected
	 */
	public function setSortingIcons($asc = null, $ascSelected = null, $desc = null, $descSelected)
	{
		if($asc !== null) $this->sortingIcons['asc'] = (string) $asc;
		if($ascSelected !== null) $this->sortingIcons['ascSelected'] = (string) $ascSelected;
		if($desc !== null) $this->sortingIcons['desc'] = (string) $desc;
		if($descSelected !== null) $this->sortingIcons['descSelected'] = (string) $descSelected;
	}


	/**
	 * Sets the sorting labels
	 *
	 * @return	void
	 * @param	string[optional] $asc
	 * @param	string[optional] $ascSelected
	 * @param	string[optional] $desc
	 * @param	string[optional] $descSelected
	 */
	public function setSortingLabels($asc = null, $ascSelected = null, $desc = null, $descSelected = null)
	{
		if($asc !== null) $this->sortingLabels['asc'] = (string) $asc;
		if($ascSelected !== null) $this->sortingLabels['ascSelected'] = (string) $ascSelected;
		if($desc !== null) $this->sortingLabels['desc'] = (string) $desc;
		if($descSelected !== null) $this->sortingLabels['descSelected'] = (string) $descSelected;
	}


	/**
	 * Sets the value to sort
	 *
	 * @return	void
	 * @param	string[optional] $value
	 */
	public function setSortParameter($value = 'desc')
	{
		$this->sortParameter = SpoonFilter::getValue($value, array('asc', 'desc'), 'asc');
	}


	/**
	 * Sets the source for this datagrid
	 *
	 * @return	void
	 * @param	SpoonDataGridSource $source
	 */
	private function setSource(SpoonDataGridSource $source)
	{
		$this->source = $source;
	}


	/**
	 * Sets the table summary
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setSummary($value)
	{
		$this->summary = (string) $value;
	}


	/**
	 * Sets the path to the template file
	 *
	 * @return	void
	 * @param	string $template
	 */
	public function setTemplate($template)
	{
		$this->template = (string) $template;
	}


	/**
	 * Defines the default url
	 *
	 * @return	void
	 * @param	string $url
	 */
	public function setURL($url)
	{
		$this->url = (string) $url;
	}
}

?>