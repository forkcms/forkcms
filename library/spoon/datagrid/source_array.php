<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author 		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dave Lens <dave@spoon-library.com>
 * @since		1.0.0
 */


/**
 * This class is used for datagrids based on array sources.
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */
class SpoonDatagridSourceArray extends SpoonDatagridSource
{
	/**
	 * Static ordering (for compare method)
	 *
	 * @var	string
	 */
	public static $order;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	array $array
	 */
	public function __construct(array $array)
	{
		 // set data
		 $this->data = (array) $array;

		// set number of results
		$this->setNumResults();
	}


	/**
	 * Apply the sorting method.
	 *
	 * @return	int
	 * @param	array $firstArray
	 * @param	array $secondArray
	 */
	public static function applySorting($firstArray, $secondArray)
	{
		if($firstArray[self::$order] < $secondArray[self::$order]) return -1;
		elseif($firstArray[self::$order] > $secondArray[self::$order]) return 1;
		else return 0;
	}


	/**
	 * Retrieve the columns.
	 *
	 * @return	array
	 */
	public function getColumns()
	{
		if($this->numResults != 0)
		{
			// get the keys for the rows
			$rowKeys = array_keys($this->data);

			// return the keys from the first row
			return array_keys($this->data[$rowKeys[0]]);
		}
	}


	/**
	 * Fetch the data as an array.
	 *
	 * @return	array
	 * @param	int[optional] $offset
	 * @param	int[optional] $limit
	 * @param	string[optional] $order
	 * @param	string[optional] $sort
	 */
	public function getData($offset = null, $limit = null, $order = null, $sort = null)
	{
		// sorting ?
		if($order !== null)
		{
			// static shizzle
			self::$order = $order;

			// apply sorting
			uasort($this->data, array('SpoonDataGridSourceArray', 'applySorting'));

			// reverse if needed?
			if($sort !== null && $sort == 'desc') $this->data = array_reverse($this->data, true);
		}

		// offset & limit
		if($offset !== null && $limit !== null)
		{
			$this->data = array_slice($this->data, $offset, $limit);
		}

		return $this->data;
	}


	/**
	 * Sets the number of results.
	 *
	 * @return	void
	 */
	private function setNumResults()
	{
		$this->numResults = (int) count($this->data);
	}
}

?>