<?php

/**
 * This is the detail widget.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
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
	 * Assign the template path
	 *
	 * @return string
	 */
	private function assignTemplate()
	{
		$template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/content_blocks/layout/widgets/default.tpl');

		// is the content block visible?
		if(!empty($this->item))
		{
			// check if the given template exists
			try
			{
				$template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/content_blocks/layout/widgets/' . $this->item['template']);
			}

			// template does not exist; use the default template
			catch(FrontendException $e)
			{
				// do nothing
			}
		}

		// set a default text so we don't see the template data
		else $this->item['text'] = '';

		return $template;
	}

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadData();
		$template = $this->assignTemplate();
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
