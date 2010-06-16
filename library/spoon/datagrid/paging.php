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


interface iSpoonDatagridPaging
{
	public static function getContent($URL, $offset, $order, $sort, $numResults, $numPerPage, $debug = true, $compileDirectory = null);
}


/**
 * This class is the base class for pagination
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */
class SpoonDatagridPaging implements iSpoonDatagridPaging
{
	/**
	 * Next label
	 *
	 * @var	string
	 */
	private static $next = 'next';


	/**
	 * Previous label
	 *
	 * @var	string
	 */
	private static $previous = 'previous';


	/**
	 * Builds & returns the pagination.
	 *
	 * @return	string
	 * @param	string $URL
	 * @param	int $offset
	 * @param	string $order
	 * @param	string $sort
	 * @param	int $numResults
	 * @param	int $numPerPage
	 * @param	bool[optional] $debug
	 * @param	string[optional] $compileDirectory
	 */
	public static function getContent($URL, $offset, $order, $sort, $numResults, $numPerPage, $debug = true, $compileDirectory = null)
	{
		// current page
		$currentPage = ceil($offset / $numPerPage) + 1;

		// number of pages
		$numPages = ceil($numResults / $numPerPage);

		// load template
		$tpl = new SpoonTemplate();

		// compile directory
		if($compileDirectory !== null) $tpl->setCompileDirectory($compileDirectory);
		else $tpl->setCompileDirectory(dirname(__FILE__));

		// force compiling
		$tpl->setForceCompile((bool) $debug);

		// previous url
		if($currentPage > 1)
		{
			// label & url
			$previousLabel = self::$previous;
			$previousURL = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset - $numPerPage), $order, $sort), $URL);
			$tpl->assign('previousLabel', $previousLabel);
			$tpl->assign('previousURL', $previousURL);
		}

		// next url
		if($currentPage < $numPages)
		{
			// label & url
			$nextLabel = self::$next;
			$nextURL = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset + $numPerPage), $order, $sort), $URL);
			$tpl->assign('nextLabel', $nextLabel);
			$tpl->assign('nextURL', $nextURL);
		}

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
				$items[$numPages - $limit - 1] = '...';
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

					// url to this page
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
		ob_start();
		$tpl->display(dirname(__FILE__) .'/paging.tpl');
		return ob_get_clean();
	}
}

?>