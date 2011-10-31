<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Add all custom stuff here.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
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
	 * @param FrontendTemplate $tpl The template instance.
	 */
	public function __construct($tpl)
	{
		$this->tpl = $tpl;
		$this->parse();
	}

	/**
	 * Parse the custom stuff
	 */
	private function parse()
	{
		// insert your custom stuff here...
	}
}
