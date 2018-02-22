<?php

namespace App\Frontend\Modules\Profiles\Actions;

use App\Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use App\Frontend\Core\Engine\Form as FrontendForm;
use App\Frontend\Core\Language\Language as FL;
use App\Frontend\Core\Engine\Navigation as FrontendNavigation;
use App\Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use App\Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;

class Login extends FrontendBaseBlock
{
    /**
     * @var FrontendForm
     */
    private $form;

    public function execute(): void
    {
        parent::execute();

        if (FrontendProfilesAuthentication::isLoggedIn()) {
            $this->redirectToPreviousPage();

            return;
        }

        $this->loadTemplate();
        $this->buildForm();
        $this->handleForm();
        $this->parse();
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('login', null, 'post', 'loginForm');
        $this->form->addText('email')->makeRequired()->setAttribute('type', 'email');
        $this->form->addPassword('password')->makeRequired();
        $this->form->addCheckbox('remember', true);
    }

    private function parse(): void
    {
        $this->form->parse($this->template);
    }

    private function validateForm(): bool
    {
        $txtEmail = $this->form->getField('email');
        $txtPassword = $this->form->getField('password');

        if (!$txtEmail->isFilled(FL::getError('EmailIsRequired'))
            || !$txtPassword->isFilled(FL::getError('PasswordIsRequired'))
            || !$txtEmail->isEmail(FL::getError('EmailIsInvalid'))) {
            return $this->form->isCorrect();
        }

        $loginStatus = FrontendProfilesAuthentication::getLoginStatus($txtEmail->getValue(), $txtPassword->getValue());

        if ($loginStatus !== FrontendProfilesAuthentication::LOGIN_ACTIVE) {
            $errorString = sprintf(
                FL::getError('Profiles' . \SpoonFilter::toCamelCase($loginStatus) . 'Login'),
                FrontendNavigation::getUrlForBlock('Profiles', 'ResendActivation')
            );

            $this->form->addError($errorString);
            $this->template->assign('loginError', $errorString);
        }

        return $this->form->isCorrect();
    }

    private function handleForm(): void
    {
        if (!$this->form->isSubmitted() || !$this->validateForm()) {
            return;
        }

        FrontendProfilesAuthentication::login(
            FrontendProfilesModel::getIdByEmail($this->form->getField('email')->getValue()),
            $this->form->getField('remember')->getChecked()
        );

        $this->redirectToPreviousPage();
    }

    private function redirectToPreviousPage(): void
    {
        $this->redirect(urldecode($this->getRequest()->query->get('queryString', SITE_URL)));
    }
}
