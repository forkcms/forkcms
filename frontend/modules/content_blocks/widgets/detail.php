<?php

/**
 * This is the detail widget.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
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
	 */
	public function execute()
	{
		parent::execute();
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

		$this->loadTemplate($template);
		$this->parse();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->item = FrontendContentBlocksModel::get((int) $this->data['id']);
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		// assign data
		$this->tpl->assign('widgetContentBlocks', $this->item);
	}
}
