<?php

class SpoonDataGridPaging
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
	 */
	public static function getContent($url, $offset, $order, $sort, $numResults, $numPerPage)
	{
		// current page
		$iCurrentPage = ceil($offset / $numPerPage) + 1;
		
		// number of pages
		$iPages = ceil($numResults / $numPerPage);
		
		// load template
		$path = pathinfo(__FILE__);
		$tpl = new SpoonTemplate($path['dirname'] .'/paging.tpl');
		
		// disable headers
		$tpl->disableHeaders();
		
		// previous url
		if($iCurrentPage > 1)
		{
			// show option
			$tpl->assignOption('oPreviousURL');
			
			// parse url
			$tpl->assign('previous.url', str_replace(array('[offset]', '[order]', '[sort]'), array(($offset - $numPerPage), $order, $sort), $url));
		}
		
		// next url
		if($iCurrentPage < $iPages)
		{
			// show option
			$tpl->assignOption('oNextURL');
			
			// parse url
			$tpl->assign('next.url', str_replace(array('[offset]', '[order]', '[sort]'), array(($offset + $numPerPage), $order, $sort), $url));
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
		
		// set iteration
		$tpl->setIteration('iPages');
		
		// loop pages
		foreach($aItems as $item)
		{
			// hellips
			if($item == '...') $tpl->assignIterationOption('oHellip');
			
			// regular page
			else 
			{
				// show page
				$tpl->assignIterationOption('oPage');
				
				// parse page
				$tpl->assignIteration('iPage', $item);
				
				// current page ?
				if($item == $iCurrentPage) $tpl->assignIterationOption('oCurrentPage');
				
				// other page
				else 
				{
					// url to this page
					$tpl->assignIteration('url', str_replace(array('[offset]', '[order]', '[sort]'), array((($numPerPage * $item) - $numPerPage), $order, $sort), $url));
					
					// show the page 
					$tpl->assignIterationOption('oOtherPage');
				}
			}
			
			// refill iteration
			$tpl->refillIteration();
		}
		
		// parse iteration
		$tpl->parseIteration();
		
		// cough it up
		return $tpl->getContent();
	}
}

?>