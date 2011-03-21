<?php

/**
 * This is a widget with the search form
 *
 * @package		frontend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class FrontendSearchWidgetForm extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// load form
		$this->loadForm();

		// parse
		$this->parse();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('search', FrontendNavigation::getURLForBlock('search'), 'get', null, false);

		// create elements
		$this->frm->addText('q_widget', null, 255, 'inputText autoSuggest', 'inputTextError autoSuggest');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);
	}
}

?>