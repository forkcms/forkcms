<?php

/**
 * Show all testimonials.
 *
 * @package		frontend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @since		2.1
 */
class FrontendTestimonialsIndex extends FrontendBaseBlock
{
	/**
	 * Execute the extra.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();
	}


	/**
	 * Parse the data and compile the template.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign the testimonials
		$this->tpl->assign('testimonialsItems', FrontendTestimonialsModel::getAll());
	}
}

?>