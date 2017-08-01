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
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;
use Frontend\Modules\Profiles\Engine\Profile;

/**
 * Change the e-mail of the current logged in profile.
 */
class ChangeEmail extends FrontendBaseBlock
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
            // profile not logged in
            $this->redirect(
                FrontendNavigation::getUrlForBlock(
                    'Profiles',
                    'Login'
                ) . '?queryString=' . FrontendNavigation::getUrlForBlock('Profiles', 'ChangeEmail'),
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
        $this->form = new FrontendForm('updateEmail', null, null, 'updateEmailForm');
        $this->form->addPassword('password')->setAttributes(['required' => null]);
        $this->form->addText('email', $this->profile->getEmail())->setAttributes(
            ['required' => null, 'type' => 'email']
        );
    }

    private function parse(): void
    {
        // have the settings been saved?
        if ($this->url->getParameter('sent') == 'true') {
            // show success message
            $this->template->assign('updateEmailSuccess', true);
        }

        // parse the form
        $this->form->parse($this->template);
    }

    private function validateForm(): void
    {
        // is the form submitted
        if ($this->form->isSubmitted()) {
            // get fields
            $txtPassword = $this->form->getField('password');
            $txtEmail = $this->form->getField('email');

            // password filled in?
            if ($txtPassword->isFilled(FL::getError('PasswordIsRequired'))) {
                // password correct?
                if (FrontendProfilesAuthentication::getLoginStatus($this->profile->getEmail(), $txtPassword->getValue()) !== FrontendProfilesAuthentication::LOGIN_ACTIVE) {
                    // set error
                    $txtPassword->addError(FL::getError('InvalidPassword'));
                }

                // email filled in?
                if ($txtEmail->isFilled(FL::getError('EmailIsRequired'))) {
                    // valid email?
                    if ($txtEmail->isEmail(FL::getError('EmailIsInvalid'))) {
                        // email already exists?
                        if (FrontendProfilesModel::existsByEmail($txtEmail->getValue(), $this->profile->getId())) {
                            // set error
                            $txtEmail->setError(FL::getError('EmailExists'));
                        }
                    }
                }
            }

            // no errors
            if ($this->form->isCorrect()) {
                // update email
                FrontendProfilesModel::update($this->profile->getId(), ['email' => $txtEmail->getValue()]);

                // redirect
                $this->redirect(
                    SITE_URL . FrontendNavigation::getUrlForBlock('Profiles', 'ChangeEmail') . '?sent=true'
                );
            } else {
                $this->template->assign('updateEmailHasFormError', true);
            }
        }
    }
}
