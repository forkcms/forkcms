<?php

/**
 * FrontendMailmotorWidgetSubscribe
 * This is a widget with the subscribe form
 *
 * @package		frontend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class FrontendMailmotorWidgetSubscribe extends FrontendBaseWidget
{
	/**
	 * Execute the extra
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
		//$this->parse();
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the subscribe form link
	}
}

?>