<?php

namespace Frontend\Modules\Profiles\Widgets;

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
    private $form;

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

        $this->form = new FrontendForm(
            'login',
            FrontendNavigation::getUrlForBlock('Profiles', 'Login') . '?queryString=' . $this->url->getQueryString()
        );
        $this->form->addText('email')->makeRequired()->setAttribute('type', 'email');
        $this->form->addPassword('password')->makeRequired();
        $this->form->addCheckbox('remember', true);

        // parse the form
        $this->form->parse($this->template);
    }

    private function parse(): void
    {
        $this->template->assign('isLoggedIn', FrontendProfilesAuthentication::isLoggedIn());

        if (FrontendProfilesAuthentication::isLoggedIn()) {
            $profile = FrontendProfilesAuthentication::getProfile();
            $this->template->assign('profile', $profile->toArray());
        }
    }
}
