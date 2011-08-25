<?php

/**
 * This is a widget
 *
 * @package		frontend
 * @subpackage	tempname
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.2
 */
class FrontendtempnameucWidgetwname extends FrontendBaseWidget
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
		$this->parse();
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{

	}
}

?>