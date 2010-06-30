<?php

/**
 * FrontendBlogWidgetArchive
 * This is a widget with the link to the archive
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogWidgetArchive extends FrontendBaseWidget
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
		// get the numbers
		$this->tpl->assign('widgetBlogArchive', FrontendBlogModel::getArchiveNumbers());
	}
}

?>