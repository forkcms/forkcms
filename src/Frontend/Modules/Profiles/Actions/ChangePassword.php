<?php

namespace Frontend\Modules\Profiles\Actions;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\Profile\Status;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

/**
 * Change the password of the current logged in profile.
 */
class ChangePassword extends FrontendBaseBlock
{
    /**
     * @var FrontendForm
     */
    private $form;

    /**
     * The current profile.
     *
     * @var Profile
     */
    private $profile;

    public function execute(): void
    {
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            throw new InsufficientAuthenticationException('You need to log in to change your email');
        }

        parent::execute();
        $this->getData();
        $this->loadTemplate();
        $this->buildForm();
        $this->handleForm();
        $this->parse();
    }

    private function getData(): void
    {
        $this->profile = FrontendProfilesAuthentication::getProfile();
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('updatePassword', null, null, 'updatePasswordForm');
        $this->form->addPassword('old_password')->makeRequired();
        $this->form->addPassword('new_password')->makeRequired()->setAttribute('data-role', 'fork-new-password');
        $this->form->addPassword('verify_new_password')->makeRequired()->setAttribute('data-role', 'fork-new-password');
        $this->form->addCheckbox('show_password')->setAttribute('data-role', 'fork-toggle-visible-password');
    }

    private function parse(): void
    {
        // show the success message when the password was changed
        $this->template->assign('updatePasswordSuccess', $this->url->getParameter('changedPassword') === 'true');
        $this->form->parse($this->template);
    }

    private function isValidLoginCredentials(string $email, string $password): bool
    {
        $loginStatus = FrontendProfilesAuthentication::getLoginStatus($email, $password);

        return $loginStatus === (string) Status::active();
    }

    private function validateForm(): bool
    {
        $txtOldPassword = $this->form->getField('old_password');
        $txtNewPassword = $this->form->getField('new_password');

        if (!$txtOldPassword->isFilled(FL::getError('PasswordIsRequired'))) {
            return false;
        }

        if (!$this->isValidLoginCredentials($this->profile->getEmail(), $txtOldPassword->getValue())) {
            $txtOldPassword->addError(FL::getError('InvalidPassword'));
        }

        if ($txtNewPassword->isFilled(FL::getError('PasswordIsRequired'))
            && $txtNewPassword->getValue() !== $this->form->getField('verify_new_password')->getValue()) {
            $this->form->getField('verify_new_password')->addError(FL::err('PasswordsDontMatch'));
        }

        return $this->form->isCorrect();
    }

    private function handleForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }

        if (!$this->validateForm()) {
            $this->template->assign('updatePasswordHasFormError', true);

            return;
        }

        FrontendProfilesAuthentication::updatePassword(
            $this->profile->getId(),
            $this->form->getField('new_password')->getValue()
        );

        $this->redirect(
            FrontendNavigation::getUrlForBlock('Profiles', 'ChangePassword') . '?changedPassword=true'
        );
    }
}
