<?php

namespace Backend\Modules\Users\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language as BL;

/**
 * This widget will show the statistics of the authenticated user.
 */
class Statistics extends BackendBaseWidget
{
    public function execute(): void
    {
        $this->setColumn('left');
        $this->setPosition(1);
        $this->parse();
        $this->display();
    }

    private function parse(): void
    {
        // get the logged in user
        $authenticatedUser = BackendAuthentication::getUser();

        // check if we need to show the password strength and parse the label
        $this->template->assign('showPasswordStrength', ($authenticatedUser->getSetting('password_strength') !== 'strong'));
        $this->template->assign('passwordStrengthLabel', BL::lbl($authenticatedUser->getSetting('password_strength')));
    }
}
