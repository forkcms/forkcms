<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget which creates a previous/next navigation for pages on the same level.
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class FrontendPagesWidgetPreviousNextNavigation extends FrontendBaseWidget
{
	/**
	 * The items.
	 *
	 * @var	array
	 */
	private $items;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadData();

		$widgetTemplatesPath = FRONTEND_MODULES_PATH . '/pages/layout/widgets';

		// check if the given template exists
		try
		{
			$template = FrontendTheme::getPath($widgetTemplatesPath . '/' . $this->data['template']);
		}

		// template does not exist; assume subpages_default.tpl
		catch(FrontendException $e)
		{
			$template = FrontendTheme::getPath($widgetTemplatesPath . '/previous_next_navigation.tpl');
		}

		$this->loadTemplate($template);
		$this->parse();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		// get the current page id
		$pageId = Spoon::get('page')->getId();

		$navigation = FrontendNavigation::getNavigation();
		$pageInfo = FrontendNavigation::getPageInfo($pageId);

		$this->navigation = array();

		if(isset($navigation['page'][$pageInfo['parent_id']]))
		{
			$pages = $navigation['page'][$pageInfo['parent_id']];

			// store
			$pagesPrev = $pages;
			$pagesNext = $pages;

			// check for current id
			foreach($pagesNext as $key => $value)
			{
				if((int) $key != (int) $pageId)
				{
					// go to next pointer in array
					next($pagesNext);
					next($pagesPrev);
				}
				else
				{
					break;
				}
			}

			// get previous page
			$this->navigation['previous'] = prev($pagesPrev);

			// get next page
			$this->navigation['next'] = next($pagesNext);

			// get parent page
			$this->navigation['parent'] = FrontendNavigation::getPageInfo($pageInfo['parent_id']);
		}
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		$this->tpl->assign('widgetPagesNavigation', $this->navigation);
	}
}
