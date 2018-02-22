<?php

namespace App\Frontend\Modules\Profiles\Actions;

use App\Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use App\Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

class Logout extends FrontendBaseBlock
{
    public function execute(): void
    {
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            FrontendProfilesAuthentication::logout();
        }

        $this->redirect(SITE_URL);
    }
}
