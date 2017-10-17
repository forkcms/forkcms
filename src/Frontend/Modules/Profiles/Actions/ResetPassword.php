<?php

namespace Frontend\Modules\Profiles\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Reset your password using a token received from the forgot_password action.
 */
class ResetPassword extends FrontendBaseBlock
{
    /**
     * @var FrontendForm
     */
    private $form;

    /** @var int */
    private $profileId;

    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();

        if ($this->url->getParameter('passwordHasBeenReset') === 'true') {
            $this->template->assign('resetPasswordSuccess', true);
            $this->template->assign('resetPasswordHideForm', true);

            return;
        }

        $this->profileId = $this->getProfileId();

        $this->buildForm();
        $this->handleForm();
        $this->form->parse($this->template);
    }

    private function getProfileId(): int
    {
        $key = $this->getResetPasswordOneTimeAccessKey();

        $profileId = FrontendProfilesModel::getIdBySetting('forgot_password_key', $key);

        if ($profileId === 0) {
            throw new NotFoundHttpException();
        }

        return $profileId;
    }

    private function getResetPasswordOneTimeAccessKey(): string
    {
        $key = $this->url->getParameter(0);

        if ($key === null) {
            throw new NotFoundHttpException();
        }

        return $key;
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('resetPassword', null, null, 'resetPasswordForm');

        $this->form->addPassword('password')->makeRequired()->setAttribute('data-role', 'fork-new-password');
        $this->form->addCheckbox('show_password')->setAttribute('data-role', 'fork-toggle-visible-password');
    }

    private function validateForm(): bool
    {
        $this->form->getField('password')->isFilled(FL::getError('PasswordIsRequired'));

        return $this->form->isCorrect();
    }

    private function cleanUpResetPasswordOneTimeAccessToken(): void
    {
        FrontendProfilesModel::deleteSetting($this->profileId, 'forgot_password_key');
    }

    private function handleForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }

        if (!$this->validateForm()) {
            $this->template->assign('forgotPasswordHasError', true);

            return;
        }

        $this->cleanUpResetPasswordOneTimeAccessToken();

        FrontendProfilesAuthentication::updatePassword($this->profileId, $this->form->getField('password')->getValue());

        $this->login();

        $this->redirect(
            FrontendNavigation::getUrlForBlock($this->getModule(), $this->getAction()) . '?passwordHasBeenReset=true'
        );
    }

    private function login(): void
    {
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            FrontendProfilesAuthentication::login($this->profileId);
        }
    }
}
