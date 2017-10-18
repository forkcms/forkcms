<?php

namespace Frontend\Modules\Profiles\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

/**
 * This is a widget with a login form
 */
class LoginLink extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        // assign if logged in
        $this->template->assign('isLoggedIn', FrontendProfilesAuthentication::isLoggedIn());

        // is logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            // get the profile
            $profile = FrontendProfilesAuthentication::getProfile();

            // assign logged in profile
            $this->template->assign('profile', $profile->toArray());
        }
    }
}
