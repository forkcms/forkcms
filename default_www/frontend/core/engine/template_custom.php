<?php

/**
 * Add all custom stuff here.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendTemplateCustom
{
	/**
	 * Template instance
	 *
	 * @var	ForkTemplate
	 */
	private $tpl;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	FrontendTemplate $tpl	The template instance.
	 */
	public function __construct($tpl)
	{
		// set property
		$this->tpl = $tpl;

		// call parse
		$this->parse();
	}


	/**
	 * Parse the custom stuff
	 *
	 * @return	void
	 */
	private function parse()
	{
		// insert your custom stuff here...
	}
}

?>