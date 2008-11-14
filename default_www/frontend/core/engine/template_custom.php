<?php
/**
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	template
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
	 * @param	ForkTemplate $tpl
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