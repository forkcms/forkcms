<?php

namespace Backend\Modules\Authentication\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\User;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the index-action (default), it will display the login screen
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class Index extends BackendBaseActionIndex
{
    /**
     * Form instances
     *
     * @var	BackendForm
     */
    private $frm;
    private $frmForgotPassword;

    /**
     * Execute the action
     */
    public function execute()
    {
        // check if the user is really logged on
        if (BackendAuthentication::getUser()->isAuthenticated()) {
            $this->redirect($this->getParameter('querystring', 'string', BackendModel::createUrlForAction(null, 'Dashboard')));
        }

        parent::execute();
        $this->load();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the forms
     */
    private function load()
    {
        $this->frm = new BackendForm(null, null, 'post', true, false);
        $this->frm->addText('backend_email');
        $this->frm->addPassword('backend_password');

        $this->frmForgotPassword = new BackendForm('forgotPassword');
        $this->frmForgotPassword->addText('backend_email_forgot');
    }

    /**
     * Parse the action into the template
     */
    public function parse()
    {
        parent::parse();

        // assign the interface language ourself, because it won't be assigned automagically
        $this->tpl->assign('INTERFACE_LANGUAGE', BL::getInterfaceLanguage());

        $this->frm->parse($this->tpl);
        $this->frmForgotPassword->parse($this->tpl);
    }

    /**
     * Validate the forms
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $txtEmail = $this->frm->getField('backend_email');
            $txtPassword = $this->frm->getField('backend_password');

            // required fields
            if (!$txtEmail->isFilled() || !$txtPassword->isFilled()) {
                // add error
                $this->frm->addError('fields required');

                // show error
                $this->tpl->assign('hasError', true);
            }

            $this->getContainer()->get('logger')->info(
                "Trying to authenticate user '{$txtEmail->getValue()}'."
            );

            // invalid form-token?
            if ($this->frm->getToken() != $this->frm->getField('form_token')->getValue()) {
                // set a correct header, so bots understand they can't mess with us.
                if (!headers_sent()) {
                    header('400 Bad Request', true, 400);
                }
            }

            // get the user's id
            $userId = BackendUsersModel::getIdByEmail($txtEmail->getValue());

            // all fields are ok?
            if ($txtEmail->isFilled() && $txtPassword->isFilled() && $this->frm->getToken() == $this->frm->getField('form_token')->getValue()) {
                // try to login the user
                if (!BackendAuthentication::loginUser($txtEmail->getValue(), $txtPassword->getValue())) {
                    $this->getContainer()->get('logger')->info(
                        "Failed authenticating user '{$txtEmail->getValue()}'."
                    );

                    // add error
                    $this->frm->addError('invalid login');

                    // store attempt in session
                    $current = (\SpoonSession::exists('backend_login_attempts')) ? (int) \SpoonSession::get('backend_login_attempts') : 0;

                    // increment and store
                    \SpoonSession::set('backend_login_attempts', ++$current);

                    // save the failed login attempt in the user's settings
                    if ($userId !== false) {
                        BackendUsersModel::setSetting($userId, 'last_failed_login_attempt', time());
                    }

                    // show error
                    $this->tpl->assign('hasError', true);
                }
            }

            // check sessions
            if (\SpoonSession::exists('backend_login_attempts') && (int) \SpoonSession::get('backend_login_attempts') >= 5) {
                // get previous attempt
                $previousAttempt = (\SpoonSession::exists('backend_last_attempt')) ? \SpoonSession::get('backend_last_attempt') : time();

                // calculate timeout
                $timeout = 5 * ((\SpoonSession::get('backend_login_attempts') - 4));

                // too soon!
                if (time() < $previousAttempt + $timeout) {
                    // sleep until the user can login again
                    sleep($timeout);

                    // set a correct header, so bots understand they can't mess with us.
                    if (!headers_sent()) {
                        header('503 Service Unavailable', true, 503);
                    }
                } else {
                    // increment and store
                    \SpoonSession::set('backend_last_attempt', time());
                }

                // too many attempts
                $this->frm->addEditor('too many attempts');

                $this->getContainer()->get('logger')->info(
                    "Too many login attempts for user '{$txtEmail->getValue()}'."
                );

                // show error
                $this->tpl->assign('hasTooManyAttemps', true);
                $this->tpl->assign('hasError', false);
            }

            // no errors in the form?
            if ($this->frm->isCorrect()) {
                // cleanup sessions
                \SpoonSession::delete('backend_login_attempts');
                \SpoonSession::delete('backend_last_attempt');

                // save the login timestamp in the user's settings
                $lastLogin = BackendUsersModel::getSetting($userId, 'current_login');
                BackendUsersModel::setSetting($userId, 'current_login', time());
                if ($lastLogin) {
                    BackendUsersModel::setSetting($userId, 'last_login', $lastLogin);
                }

                // create filter with modules which may not be displayed
                $filter = array('Authentication', 'Error', 'Core');

                // get all modules
                $modules = array_diff(BackendModel::getModules(), $filter);

                // redirect to the dashboard module if possible
                if (BackendAuthentication::isAllowedModule('Dashboard')) {
                    $module = 'Dashboard';
                } else {
                    // if not allowed in the dashboard, redirect to the first allowed module
                    foreach ($modules as $module) {
                        if (BackendAuthentication::isAllowedModule($module)) {
                            break;
                        }
                    }
                }

                $this->getContainer()->get('logger')->info(
                    "Successfully authenticated user '{$txtEmail->getValue()}'."
                );

                // redirect to the correct URL (URL the user was looking for or fallback)
                $this->redirect($this->getParameter('querystring', 'string', BackendModel::createUrlForAction(null, $module)));
            }
        }

        // is the form submitted
        if ($this->frmForgotPassword->isSubmitted()) {
            // backend email
            $email = $this->frmForgotPassword->getField('backend_email_forgot')->getValue();

            // required fields
            if ($this->frmForgotPassword->getField('backend_email_forgot')->isEmail(BL::err('EmailIsInvalid'))) {
                // check if there is a user with the given emailaddress
                if (!BackendUsersModel::existsEmail($email)) {
                    $this->frmForgotPassword->getField('backend_email_forgot')->addError(BL::err('EmailIsUnknown'));
                }
            }

            // no errors in the form?
            if ($this->frmForgotPassword->isCorrect()) {
                // generate the key for the reset link and fetch the user ID for this email
                $key = BackendAuthentication::getEncryptedString($email, uniqid());

                // insert the key and the timestamp into the user settings
                $userId = BackendUsersModel::getIdByEmail($email);
                $user = new User($userId);
                $user->setSetting('reset_password_key', $key);
                $user->setSetting('reset_password_timestamp', time());

                // variables to parse in the e-mail
                $variables['resetLink'] = SITE_URL . BackendModel::createURLForAction('ResetPassword') . '&email=' . $email . '&key=' . $key;

                // send e-mail to user
                $this->get('mailer')->addEmail(\SpoonFilter::ucfirst(BL::msg('ResetYourPasswordMailSubject')), BACKEND_MODULE_PATH . '/Layout/Templates/Mails/ResetPassword.tpl', $variables, $email);

                // clear post-values
                $_POST['backend_email_forgot'] = '';

                // show success message
                $this->tpl->assign('isForgotPasswordSuccess', true);

                // show form
                $this->tpl->assign('showForm', true);
            } else {
                // errors?
                $this->tpl->assign('showForm', true);
            }
        }
    }
}
