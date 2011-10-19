<?php

/**
 * This is the detail widget.
 *
 * @package		frontend
 * @subpackage	content_blocks
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class FrontendContentBlocksWidgetDetail extends FrontendBaseWidget
{
	/**
	 * The item.
	 *
	 * @var	array
	 */
	private $item;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// parent execute
		parent::execute();

		// load data
		$this->loadData();

		// check if the given template exists
		try
		{
			$template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/content_blocks/layout/widgets/' . $this->item['template']);
		}

		// template does not exist; assume default.tpl
		catch(FrontendException $e)
		{
			$template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/content_blocks/layout/widgets/default.tpl');
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
		// fetch the item
		$this->item = FrontendContentBlocksModel::get((int) $this->data['id']);
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign data
		$this->tpl->assign('widgetContentBlocks', $this->item);
	}
}

?>