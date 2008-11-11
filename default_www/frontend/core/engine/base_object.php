<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBaseObject
{
	/**
	 * Template instance
	 *
	 * @var	ForkTemplate
	 */
	protected $tpl;


	/**
	 * Url instance
	 *
	 * @var	FrontendUrl
	 */
	protected $url;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// set properties
		$this->tpl = Spoon::getObjectReference('template');
		$this->url = Spoon::getObjectReference('url');
	}
}
?>