<?php

/**
 * This is a widget with recent blog-articles
 *
 * @package		frontend
 * @subpackage	faq
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class FrontendFaqWidgetMostRead extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// only show on the default action
		if(!strpos(FrontendNavigation::getURLForBlock('faq'), $this->URL->getQueryString())) return;

		// parse
		$this->parse();
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign comments
		$this->tpl->assign('widgetFaqMostRead', FrontendFaqModel::getMostRead(FrontendModel::getModuleSetting('faq', 'most_read_num_items', 10)));
	}
}

?>