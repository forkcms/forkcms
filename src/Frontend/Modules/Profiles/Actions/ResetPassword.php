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

/**
 * Reset your password using a token received from the forgot_password action.
 */
class ResetPassword extends FrontendBaseBlock
{
    /**
     * FrontendForm instance.
     *
     * @var FrontendForm
     */
    private $form;

    public function execute(): void
    {
        // get reset key
        $key = $this->url->getParameter(0);

        // do we have an reset key?
        if (isset($key)) {
            // load parent
            parent::execute();

            // load template
            $this->loadTemplate();

            // get profile id
            $profileId = FrontendProfilesModel::getIdBySetting('forgot_password_key', $key);

            // have id?
            if ($profileId !== 0) {
                // load
                $this->buildForm();

                // validate
                $this->validateForm();
            } elseif ($this->url->getParameter('sent') != 'true') {
                $this->redirect(FrontendNavigation::getUrl(404));
            }

            // parse
            $this->parse();
        } else {
            $this->redirect(FrontendNavigation::getUrl(404));
        }
    }

    private function buildForm(): void
    {
        // create the form
        $this->form = new FrontendForm('resetPassword', null, null, 'resetPasswordForm');

        // create & add elements
        $this->form->addPassword('password')->setAttributes(
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
        // has the password been saved?
        if ($this->url->getParameter('sent') == 'true') {
            // show message
            $this->template->assign('resetPasswordSuccess', true);

            // hide form
            $this->template->assign('resetPasswordHideForm', true);
        } else {
            $this->form->parse($this->template);
        }
    }

    private function validateForm(): void
    {
        // is the form submitted
        if ($this->form->isSubmitted()) {
            // get fields
            $txtPassword = $this->form->getField('password');

            // field is filled in?
            $txtPassword->isFilled(FL::getError('PasswordIsRequired'));

            // valid
            if ($this->form->isCorrect()) {
                // get profile id
                $profileId = FrontendProfilesModel::getIdBySetting('forgot_password_key', $this->url->getParameter(0));

                // remove key (we can only update the password once with this key)
                FrontendProfilesModel::deleteSetting($profileId, 'forgot_password_key');

                // update password
                FrontendProfilesAuthentication::updatePassword($profileId, $txtPassword->getValue());

                // login (check again because we might have logged in in the meanwhile)
                if (!FrontendProfilesAuthentication::isLoggedIn()) {
                    FrontendProfilesAuthentication::login($profileId);
                }

                // redirect
                $this->redirect(
                    FrontendNavigation::getUrlForBlock('Profiles', 'ResetPassword') . '/' . $this->url->getParameter(
                        0
                    ) . '?sent=true'
                );
            } else {
                $this->template->assign('forgotPasswordHasError', true);
            }
        }
    }
}
