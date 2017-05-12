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
    private $frm;

    public function execute(): void
    {
        // get reset key
        $key = $this->URL->getParameter(0);

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
            } elseif ($this->URL->getParameter('sent') != 'true') {
                $this->redirect(FrontendNavigation::getURL(404));
            }

            // parse
            $this->parse();
        } else {
            $this->redirect(FrontendNavigation::getURL(404));
        }
    }

    private function buildForm(): void
    {
        // create the form
        $this->frm = new FrontendForm('resetPassword', null, null, 'resetPasswordForm');

        // create & add elements
        $this->frm->addPassword('password')->setAttributes(
            [
                'required' => null,
                'data-role' => 'fork-new-password',
            ]
        );
        $this->frm->addCheckbox('show_password')->setAttributes(
            ['data-role' => 'fork-toggle-visible-password']
        );
    }

    private function parse(): void
    {
        // has the password been saved?
        if ($this->URL->getParameter('sent') == 'true') {
            // show message
            $this->tpl->assign('resetPasswordSuccess', true);

            // hide form
            $this->tpl->assign('resetPasswordHideForm', true);
        } else {
            $this->frm->parse($this->tpl);
        }
    }

    private function validateForm(): void
    {
        // is the form submitted
        if ($this->frm->isSubmitted()) {
            // get fields
            $txtPassword = $this->frm->getField('password');

            // field is filled in?
            $txtPassword->isFilled(FL::getError('PasswordIsRequired'));

            // valid
            if ($this->frm->isCorrect()) {
                // get profile id
                $profileId = FrontendProfilesModel::getIdBySetting('forgot_password_key', $this->URL->getParameter(0));

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
                    FrontendNavigation::getURLForBlock('Profiles', 'ResetPassword') . '/' . $this->URL->getParameter(
                        0
                    ) . '?sent=true'
                );
            } else {
                $this->tpl->assign('forgotPasswordHasError', true);
            }
        }
    }
}
