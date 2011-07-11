<?php

/**
 * This is a widget wherin the sitemap lives
 *
 * @package		frontend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendPagesWidgetChildren extends FrontendBaseWidget
{
	/**
	 * The items.
	 *
	 * @var	array
	 */
	private $items;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load data
		$this->loadData();

		// check if the given template exists
		try
		{
			$template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/pages/layout/widgets/' . $this->data['template']);
		}

		// template does not exist; assume default.tpl
		catch(FrontendException $e)
		{
			$template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/pages/layout/widgets/children_default.tpl');
		}

		// load template
		$this->loadTemplate($template);

		// parse
		$this->parse();
	}


	/**
	 * Load the data
	 *
	 * @return	void
	 */
	private function loadData()
	{
		// get the current page id
		$pageId = FrontendNavigation::getPageId(SITE_MULTILANGUAGE ? substr($this->URL->getQueryString(), 3) : $this->URL->getQueryString());

		// fetch the items
		$this->items = FrontendPagesModel::getChildrenForBlocks($pageId);
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign data
		$this->tpl->assign('widgetChildren', $this->items);
	}
}

?>