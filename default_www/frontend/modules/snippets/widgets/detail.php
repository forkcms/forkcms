<?php

/**
 * FrontendSnippetsDetail
 *
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	snippets
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendSnippetsWidgetDetail extends FrontendBaseWidget
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
		// load template
		$this->loadTemplate();

		// load data
		$this->loadData();

		// parse
		$this->parse();
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
		$this->snippet = FrontendSnippetsModel::get((int) $this->data['id']);
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign
		if(!empty($this->snippet))
		{
			// add an extra item so we can use it as an option
			$this->snippet['is'. $this->snippet['id']] = true;

			// assign
			$this->tpl->assign('snippet', $this->snippet);
		}
	}

}
?>