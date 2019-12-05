<?php

namespace Frontend\Modules\Profiles\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

class Logout extends FrontendBaseBlock
{
    public function execute(): void
    {
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            FrontendProfilesAuthentication::logout();
        }

        $this->redirect(SITE_MULTILANGUAGE ? SITE_URL . '/' . LANGUAGE : SITE_URL);
    }
}
