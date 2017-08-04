<?php

namespace Frontend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Profile;

/**
 * Change the password of the current logged in profile.
 */
class ChangePassword extends FrontendBaseBlock
{
    /**
     * FrontendForm instance.
     *
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
        // profile logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            parent::execute();
            $this->getData();
            $this->loadTemplate();
            $this->buildForm();
            $this->validateForm();
            $this->parse();
        } else {
            $this->redirect(
                FrontendNavigation::getUrlForBlock(
                    'Profiles',
                    'Login'
                ) . '?queryString=' . FrontendNavigation::getUrlForBlock('Profiles', 'ChangePassword'),
                307
            );
        }
    }

    private function getData(): void
    {
        // get profile
        $this->profile = FrontendProfilesAuthentication::getProfile();
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('updatePassword', null, null, 'updatePasswordForm');
        $this->form->addPassword('old_password')->setAttributes(['required' => null]);
        $this->form->addPassword('new_password')->setAttributes(
            [
                'required' => null,
                'data-role' => 'fork-new-password',
            ]
        );
        $this->form->addPassword('verify_new_password')->setAttributes(
            [
                'required' => null,
                'data-role' => 'fork-new-password',
            ]
        );
        $this->form->addCheckbox('show_password')->setAttributes(
            ['data-role' => 'fork-toggle-visible-password']
        );
    }

    private function parse(): void
    {
        // have the settings been saved?
        if ($this->url->getParameter('sent') == 'true') {
            // show success message
            $this->template->assign('updatePasswordSuccess', true);
        }

        // parse the form
        $this->form->parse($this->template);
    }

    private function validateForm(): void
    {
        // is the form submitted
        if ($this->form->isSubmitted()) {
            // get fields
            $txtOldPassword = $this->form->getField('old_password');
            $txtNewPassword = $this->form->getField('new_password');

            // old password filled in?
            if ($txtOldPassword->isFilled(FL::getError('PasswordIsRequired'))) {
                // old password correct?
                if (FrontendProfilesAuthentication::getLoginStatus($this->profile->getEmail(), $txtOldPassword->getValue()) !== FrontendProfilesAuthentication::LOGIN_ACTIVE) {
                    // set error
                    $txtOldPassword->addError(FL::getError('InvalidPassword'));
                }

                // new password filled in?
                $txtNewPassword->isFilled(FL::getError('PasswordIsRequired'));

                // passwords match?
                if ($this->form->getField('new_password')->getValue() !== $this->form->getField('verify_new_password')->getValue()) {
                    $this->form->getField('verify_new_password')->addError(FL::err('PasswordsDontMatch'));
                }
            }

            // no errors
            if ($this->form->isCorrect()) {
                // update password
                FrontendProfilesAuthentication::updatePassword($this->profile->getId(), $txtNewPassword->getValue());

                // redirect
                $this->redirect(
                    SITE_URL . FrontendNavigation::getUrlForBlock('Profiles', 'ChangePassword') . '?sent=true'
                );
            } else {
                $this->template->assign('updatePasswordHasFormError', true);
            }
        }
    }
}
