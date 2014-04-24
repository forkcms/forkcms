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
use Backend\Core\Engine\Language as BL;

/**
 * This widget will show the statistics of the authenticated user.
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class Statistics extends BackendBaseWidget
{
    /**
     * Execute the widget
     */
    public function execute()
    {
        $this->setColumn('left');
        $this->setPosition(1);
        $this->parse();
        $this->display();
    }

    /**
     * Parse into template
     */
    private function parse()
    {
        // get the logged in user
        $authenticatedUser = BackendAuthentication::getUser();

        // check if we need to show the password strength and parse the label
        $this->tpl->assign('showPasswordStrength', ($authenticatedUser->getSetting('password_strength') !== 'strong'));
        $this->tpl->assign('passwordStrengthLabel', BL::lbl($authenticatedUser->getSetting('password_strength')));
    }
}
