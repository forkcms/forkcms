<?php

/**
 * This is the index-action, it can be used as a dashboard.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendProfilesIndex extends FrontendBaseBlock
{
	/**
	 * Execute the extra.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// no url parameter
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			// call the parent
			parent::execute();

			/*
			 * You could use this as some kind of dashboard where you could show an activity stream, some statistics, ...
			 */

			// load template
			$this->loadTemplate();
		}

		// only if you are logged in, baby.
		else $this->redirect(FrontendNavigation::getURL(404));
	}
}

?>
