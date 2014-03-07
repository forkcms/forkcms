<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with a login form
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class FrontendProfilesWidgetLoginLink extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse
	 */
	private function parse()
	{
	    // assign if logged in
		$this->tpl->assign('isLoggedIn', FrontendProfilesAuthentication::isLoggedIn());

        // is logged in
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			// get the profile
			/**
			 * @var FrontendProfilesProfile
			 */
			$profile = FrontendProfilesAuthentication::getProfile();

			$this->tpl->assign('profile', $profile->toArray());
		}
	}
}
