<?php

namespace ForkCMS\Frontend\Modules\Profiles\Actions;

use ForkCMS\Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use ForkCMS\Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

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
