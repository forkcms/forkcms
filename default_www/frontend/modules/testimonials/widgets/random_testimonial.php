<?php

/**
 * Show a random testimonial.
 *
 * @package		frontend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @since		2.1
 */
class FrontendTestimonialsWidgetRandomTestimonial extends FrontendBaseWidget
{
	/**
	 * Execute the extra.
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
	 * Parse the template.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign the random testimonial
		$this->tpl->assign('widgetTestimonialsRandomTestimonial', FrontendTestimonialsModel::getRandom());
	}
}

?>