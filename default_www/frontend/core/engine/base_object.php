<?php

/**
 * Fork
 *
 * This is the base-object
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
		// get template from reference
		$this->tpl = Spoon::getObjectReference('template');

		// get url from reference
		$this->url = Spoon::getObjectReference('url');
	}
}
?>