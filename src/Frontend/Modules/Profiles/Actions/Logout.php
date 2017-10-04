<?php

namespace Frontend\Modules\Profiles\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

/**
 * This is the logout-action.
 */
class Logout extends FrontendBaseBlock
{
    public function execute(): void
    {
        // logout
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            FrontendProfilesAuthentication::logout();
        }

        // redirect
        $this->redirect(SITE_URL);
    }
}
