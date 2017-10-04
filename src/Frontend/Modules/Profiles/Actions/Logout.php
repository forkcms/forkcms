<?php

namespace Frontend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

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
