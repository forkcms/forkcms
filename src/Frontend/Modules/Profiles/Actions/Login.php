<?php

namespace Frontend\Modules\Profiles\Actions;

use Backend\Modules\Profiles\Domain\Profile\Status;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Navigation;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;

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
        $action = Navigation::getUrlForBlock('Profiles', 'Login');
        if ($this->getRequest()->query->has('queryString')) {
            $action .= '?queryString=' . $this->getRequest()->query->get('queryString');
        }
        $this->form = new FrontendForm('login', $action, 'post', 'loginForm');
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

        $loginStatus = Status::fromString(
            FrontendProfilesAuthentication::getLoginStatus($txtEmail->getValue(), $txtPassword->getValue())
        );

        $profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());
        if ($profileId !== null && $loginStatus->isInvalid()) {
            $loginAttempts = (int) FrontendProfilesModel::getSetting($profileId, 'login_attempts');

            FrontendProfilesModel::setSetting($profileId, 'login_attempts', ++$loginAttempts);
            if ($loginAttempts >= 10) {
                $loginStatus = Status::blocked();
                FrontendProfilesModel::update($profileId, ['status' => $loginStatus]);
            }
        }

        if (!$loginStatus->isActive()) {
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

        $profileId = FrontendProfilesModel::getIdByEmail($this->form->getField('email')->getValue());
        FrontendProfilesModel::setSetting($profileId, 'login_attempts', 0);

        FrontendProfilesAuthentication::login(
            $profileId,
            $this->form->getField('remember')->getChecked()
        );

        $this->redirectToPreviousPage();
    }

    private function redirectToPreviousPage(): void
    {
        $this->redirect(
            $this->sanitizeQueryString(
                urldecode(
                    $this->getRequest()->query->get(
                        'queryString',
                        SITE_MULTILANGUAGE ? SITE_URL . '/' . LANGUAGE : SITE_URL
                    )
                )
            )
        );
    }

    private function sanitizeQueryString(string $queryString): string
    {
        if (!preg_match('/^\//', $queryString) or preg_match('/^\/[^a-zA-Z0-9.-_~]/', $queryString)) {
            return SITE_MULTILANGUAGE ? SITE_URL . '/' . LANGUAGE : SITE_URL;
        }

        return filter_var($queryString, FILTER_SANITIZE_URL);
    }
}
