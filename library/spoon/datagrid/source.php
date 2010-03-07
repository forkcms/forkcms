<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		1.0.0
 */


/**
 * This class is the base class for sources used with datagrids
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.0.0
 */
class SpoonDatagridSource
{
	/**
	 * Final data
	 *
	 * @var	array
	 */
	protected $data = array();


	/**
	 * Number of results
	 *
	 * @var	int
	 */
	protected $numResults = 0;


	/**
	 * Fetch the number of results
	 *
	 * @return	int
	 */
	public function getNumResults()
	{
		return $this->numResults;
	}
}

?>