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

    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->buildForm();
        $this->parse();
    }

    private function buildForm(): void
    {
        // don't show the form if someone is logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            return;
        }

        $this->frm = new FrontendForm(
            'login',
            FrontendNavigation::getURLForBlock('Profiles', 'Login') . '?queryString=' . $this->URL->getQueryString()
        );
        $this->frm->addText('email')->setAttributes(['required' => null, 'type' => 'email']);
        $this->frm->addPassword('password')->setAttributes(['required' => null]);
        $this->frm->addCheckbox('remember', true);

        // parse the form
        $this->frm->parse($this->tpl);
    }

    private function parse(): void
    {
        $this->tpl->assign('isLoggedIn', FrontendProfilesAuthentication::isLoggedIn());

        if (FrontendProfilesAuthentication::isLoggedIn()) {
            $profile = FrontendProfilesAuthentication::getProfile();
            $this->tpl->assign('profile', $profile->toArray());
        }
    }
}
