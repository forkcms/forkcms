<?php

namespace Frontend\Modules\Profiles\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

/**
 * This is a widget with a login form
 */
class LoginBox extends FrontendBaseWidget
{
    /**
     * @var FrontendForm
     */
    private $frm;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->loadForm();
        $this->parse();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // don't show the form if someone is logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            return;
        }

        $this->frm = new FrontendForm(
            'login',
            FrontendNavigation::getURLForBlock('Profiles', 'Login') . '?queryString=' . $this->URL->getQueryString()
        );
        $this->frm->addText('email')->setAttributes(array('required' => null, 'type' => 'email'));
        $this->frm->addPassword('password')->setAttributes(array('required' => null));
        $this->frm->addCheckbox('remember', true);

        // parse the form
        $this->frm->parse($this->tpl);
    }

    /**
     * Parse
     */
    private function parse()
    {
        $this->tpl->assign('isLoggedIn', FrontendProfilesAuthentication::isLoggedIn());

        if (FrontendProfilesAuthentication::isLoggedIn()) {
            $profile = FrontendProfilesAuthentication::getProfile();
            $this->tpl->assign('profile', $profile->toArray());
        }
    }
}
