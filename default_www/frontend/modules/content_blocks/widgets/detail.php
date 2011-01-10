<?php

/**
 * This is the detail widget.
 *
 * @package		frontend
 * @subpackage	content_blocks
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
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

		// parse
		return $this->parse();
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
		return (!empty($this->item['text'])) ? $this->item['text'] : '';
	}
}

?>