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
use Frontend\Core\Engine\Model as FrontendModel;
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
    private $frm;

    /**
     * The current profile.
     *
     * @var Profile
     */
    private $profile;

    /**
     * Execute the extra.
     */
    public function execute()
    {
        // profile logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            parent::execute();
            $this->getData();
            $this->loadTemplate();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
        } else {
            // profile not logged in
            $this->redirect(
                FrontendNavigation::getURLForBlock(
                    'Profiles',
                    'Login'
                ) . '?queryString=' . FrontendNavigation::getURLForBlock('Profiles', 'ChangeEmail'),
                307
            );
        }
    }

    /**
     * Get profile data.
     */
    private function getData()
    {
        // get profile
        $this->profile = FrontendProfilesAuthentication::getProfile();
    }

    /**
     * Load the form.
     */
    private function loadForm()
    {
        $this->frm = new FrontendForm('updateEmail', null, null, 'updateEmailForm');
        $this->frm->addPassword('password')->setAttributes(array('required' => null));
        $this->frm->addText('email', $this->profile->getEmail())->setAttributes(
            array('required' => null, 'type' => 'email')
        );
    }

    /**
     * Parse the data into the template.
     */
    private function parse()
    {
        // have the settings been saved?
        if ($this->URL->getParameter('sent') == 'true') {
            // show success message
            $this->tpl->assign('updateEmailSuccess', true);
        }

        // parse the form
        $this->frm->parse($this->tpl);
    }

    /**
     * Validate the form.
     */
    private function validateForm()
    {
        // is the form submitted
        if ($this->frm->isSubmitted()) {
            // get fields
            $txtPassword = $this->frm->getField('password');
            $txtEmail = $this->frm->getField('email');

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
            if ($this->frm->isCorrect()) {
                // update email
                FrontendProfilesModel::update($this->profile->getId(), array('email' => $txtEmail->getValue()));

                // redirect
                $this->redirect(
                    SITE_URL . FrontendNavigation::getURLForBlock('Profiles', 'ChangeEmail') . '?sent=true'
                );
            } else {
                $this->tpl->assign('updateEmailHasFormError', true);
            }
        }
    }
}
