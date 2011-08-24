<?php

/**
 * This is the index-action (default), it will display the overview of tempname posts
 *
 * @package		frontend
 * @subpackage	tempname
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.2
 */
class FrontendtempnameucIndex extends FrontendBaseBlock
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the template
		$this->loadTemplate();
	}
}

?>