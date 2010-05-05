<?php

/**
 * FrontendContentblocksDetail
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	contentblocks
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendContentblocksWidgetDetail extends FrontendBaseWidget
{
	/**
	 * The snippet
	 *
	 * @var	array
	 */
	private $snippet;


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
	 * @return void
	 */
	private function loadData()
	{
		// validate id
		if(!isset($this->data['id'])) throw new FrontendException('SnippetId not available');

		// get the snippet
		$this->snippet = FrontendContentblocksModel::get((int) $this->data['id']);
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		return (!empty($this->snippet['content'])) ? $this->snippet['content'] : '';
	}
}

?>