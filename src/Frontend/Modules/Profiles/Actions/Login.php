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
 * This is the login-action.
 */
class Login extends FrontendBaseBlock
{
    /**
     * FrontendForm instance.
     *
     * @var FrontendForm
     */
    private $form;

    public function execute(): void
    {
        parent::execute();

        // profile not logged in
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            $this->loadTemplate();
            $this->buildForm();
            $this->validateForm();
            $this->parse();
        } else {
            // profile already logged in
            // query string
            $queryString = urldecode($this->getRequest()->query->get('queryString', SITE_URL));

            // redirect
            $this->redirect($queryString);
        }
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('login', null, 'post', 'loginForm');
        $this->form->addText('email')->setAttributes(['required' => null, 'type' => 'email']);
        $this->form->addPassword('password')->setAttributes(['required' => null]);
        $this->form->addCheckbox('remember', true);
    }

    private function parse(): void
    {
        $this->form->parse($this->template);
    }

    private function validateForm(): void
    {
        // is the form submitted
        if ($this->form->isSubmitted()) {
            // get fields
            $txtEmail = $this->form->getField('email');
            $txtPassword = $this->form->getField('password');
            $chkRemember = $this->form->getField('remember');

            // required fields
            $txtEmail->isFilled(FL::getError('EmailIsRequired'));
            $txtPassword->isFilled(FL::getError('PasswordIsRequired'));

            // both fields filled in
            if ($txtEmail->isFilled() && $txtPassword->isFilled()) {
                // valid email?
                if ($txtEmail->isEmail(FL::getError('EmailIsInvalid'))) {
                    // get the status for the given login
                    $loginStatus = FrontendProfilesAuthentication::getLoginStatus(
                        $txtEmail->getValue(),
                        $txtPassword->getValue()
                    );

                    // valid login?
                    if ($loginStatus !== FrontendProfilesAuthentication::LOGIN_ACTIVE) {
                        // get the error string to use
                        $errorString = sprintf(
                            FL::getError('Profiles' . \SpoonFilter::toCamelCase($loginStatus) . 'Login'),
                            FrontendNavigation::getUrlForBlock('Profiles', 'ResendActivation')
                        );

                        // add the error to stack
                        $this->form->addError($errorString);

                        // add the error to the template variables
                        $this->template->assign('loginError', $errorString);
                    }
                }
            }

            // valid login
            if ($this->form->isCorrect()) {
                // get profile id
                $profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());

                // login
                FrontendProfilesAuthentication::login($profileId, $chkRemember->getChecked());

                // query string
                $queryString = urldecode($this->getRequest()->query->get('queryString', SITE_URL));

                // redirect
                $this->redirect($queryString);
            }
        }
    }
}
