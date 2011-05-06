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

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();

		// check if the given template exists
		try
		{
			$template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/content_blocks/layout/widgets/' . $this->item['template']);
		}

		// template does not exist; assume default.tpl
		catch(Exception $e)
		{
			$template = FRONTEND_MODULES_PATH . '/content_blocks/layout/widgets/default.tpl';
		}

		// display the widget
		return $this->tpl->getContent($template);
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
	 * Load the template
	 *
	 * @return	void
	 * @param	string[optional] $path		The path for the template to use.
	 */
	protected function loadTemplate($path = null)
	{
		// redefine (and trick codesniffer)
		$path = ($path !== null) ? (string) $path : null;

		// overwrite the template with an empty one
		$this->tpl = new FrontendTemplate(false);
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