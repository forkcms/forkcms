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


/**
 * This class is the base class for pagination
 *
 * @package			html
 * @subpackage		datagrid
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
class SpoonDataGridPaging
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
			$previousLabel = self::$previous;
			$previousURL = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset - $numPerPage), $order, $sort), $url);
			$tpl->assign('previousLabel', $previousLabel);
			$tpl->assign('previousURL', $previousURL);
		}

		// next url
		if($iCurrentPage < $iPages)
		{
			// label & url
			$nextLabel = self::$next;
			$nextURL = str_replace(array('[offset]', '[order]', '[sort]'), array(($offset + $numPerPage), $order, $sort), $url);
			$tpl->assign('nextLabel', $nextLabel);
			$tpl->assign('nextURL', $nextURL);
		}

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
		ob_start();
		$tpl->display(dirname(__FILE__) .'/paging.tpl');
		return ob_get_clean();
	}
}

?>