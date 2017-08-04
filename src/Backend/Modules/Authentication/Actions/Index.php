<?php

namespace Backend\Modules\Authentication\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\User;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;
use Common\Mailer\Message;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * This is the index-action (default), it will display the login screen
 */
class Index extends BackendBaseActionIndex
{
    /**
     * @var BackendForm
     */
    private $form;

    /**
     * @var BackendForm
     */
    private $formForgotPassword;

    public function execute(): void
    {
        // check if the user is really logged on
        if (BackendAuthentication::getUser()->isAuthenticated()) {
            $userEmail = BackendAuthentication::getUser()->getEmail();
            $this->getContainer()->get('logger')->info(
                "User '{$userEmail}' is already authenticated."
            );
            $this->redirectToAllowedModuleAndAction();
        }

        parent::execute();
        $this->buildForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function buildForm(): void
    {
        $this->form = new BackendForm(null, null, 'post', true, false);
        $this->form
            ->addText('backend_email')
            ->setAttribute('placeholder', \SpoonFilter::ucfirst(BL::lbl('Email')))
            ->setAttribute('type', 'email')
        ;
        $this->form
            ->addPassword('backend_password')
            ->setAttribute('placeholder', \SpoonFilter::ucfirst(BL::lbl('Password')))
        ;

        $this->formForgotPassword = new BackendForm('forgotPassword');
        $this->formForgotPassword->addText('backend_email_forgot');
    }

    public function parse(): void
    {
        parent::parse();

        // assign the interface language ourself, because it won't be assigned automagically
        $this->template->assign('INTERFACE_LANGUAGE', BL::getInterfaceLanguage());

        $this->form->parse($this->template);
        $this->formForgotPassword->parse($this->template);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $txtEmail = $this->form->getField('backend_email');
            $txtPassword = $this->form->getField('backend_password');

            // required fields
            if (!$txtEmail->isFilled() || !$txtPassword->isFilled()) {
                // add error
                $this->form->addError('fields required');

                // show error
                $this->template->assign('hasError', true);
            }

            $this->getContainer()->get('logger')->info(
                "Trying to authenticate user '{$txtEmail->getValue()}'."
            );

            // invalid form-token?
            if ($this->form->getToken() != $this->form->getField('form_token')->getValue()) {
                // set a correct header, so bots understand they can't mess with us.
                throw new BadRequestHttpException();
            }

            // get the user's id
            $userId = BackendUsersModel::getIdByEmail($txtEmail->getValue());

            // all fields are ok?
            if ($txtEmail->isFilled() && $txtPassword->isFilled() && $this->form->getToken() == $this->form->getField('form_token')->getValue()) {
                // try to login the user
                if (!BackendAuthentication::loginUser($txtEmail->getValue(), $txtPassword->getValue())) {
                    $this->getContainer()->get('logger')->info(
                        "Failed authenticating user '{$txtEmail->getValue()}'."
                    );

                    // add error
                    $this->form->addError('invalid login');

                    // store attempt in session
                    $current = (int) BackendModel::getSession()->get('backend_login_attempts', 0);

                    // increment and store
                    BackendModel::getSession()->set('backend_login_attempts', ++$current);

                    // save the failed login attempt in the user's settings
                    if ($userId !== false) {
                        BackendUsersModel::setSetting($userId, 'last_failed_login_attempt', time());
                    }

                    // show error
                    $this->template->assign('hasError', true);
                }
            }

            // check sessions
            if (BackendModel::getSession()->get('backend_login_attempts', 0) >= 5) {
                // get previous attempt
                $previousAttempt = BackendModel::getSession()->get('backend_last_attempt', time());

                // calculate timeout
                $timeout = 5 * (BackendModel::getSession()->get('backend_login_attempts') - 4);

                // too soon!
                if (time() < $previousAttempt + $timeout) {
                    // sleep until the user can login again
                    sleep($timeout);

                    // set a correct header, so bots understand they can't mess with us.
                    throw new ServiceUnavailableHttpException();
                }
                // increment and store
                BackendModel::getSession()->set('backend_last_attempt', time());

                // too many attempts
                $this->form->addEditor('too many attempts');

                $this->getContainer()->get('logger')->info(
                    "Too many login attempts for user '{$txtEmail->getValue()}'."
                );

                // show error
                $this->template->assign('hasTooManyAttemps', true);
                $this->template->assign('hasError', false);
            }

            // no errors in the form?
            if ($this->form->isCorrect()) {
                // cleanup sessions
                BackendModel::getSession()->remove('backend_login_attempts');
                BackendModel::getSession()->remove('backend_last_attempt');

                // save the login timestamp in the user's settings
                $lastLogin = BackendUsersModel::getSetting($userId, 'current_login');
                BackendUsersModel::setSetting($userId, 'current_login', time());
                if ($lastLogin) {
                    BackendUsersModel::setSetting($userId, 'last_login', $lastLogin);
                }

                $this->getContainer()->get('logger')->info(
                    "Successfully authenticated user '{$txtEmail->getValue()}'."
                );

                // redirect to the correct URL (URL the user was looking for or fallback)
                $this->redirectToAllowedModuleAndAction();
            }
        }

        // is the form submitted
        if ($this->formForgotPassword->isSubmitted()) {
            // backend email
            $email = $this->formForgotPassword->getField('backend_email_forgot')->getValue();

            // required fields
            if ($this->formForgotPassword->getField('backend_email_forgot')->isEmail(BL::err('EmailIsInvalid'))) {
                // check if there is a user with the given emailaddress
                if (!BackendUsersModel::existsEmail($email)) {
                    $this->formForgotPassword->getField('backend_email_forgot')->addError(BL::err('EmailIsUnknown'));
                }
            }

            // no errors in the form?
            if ($this->formForgotPassword->isCorrect()) {
                // generate the key for the reset link and fetch the user ID for this email
                $key = BackendAuthentication::getEncryptedString($email, uniqid('', true));

                // insert the key and the timestamp into the user settings
                $userId = BackendUsersModel::getIdByEmail($email);
                $user = new User($userId);
                $user->setSetting('reset_password_key', $key);
                $user->setSetting('reset_password_timestamp', time());

                // send e-mail to user
                $from = $this->get('fork.settings')->get('Core', 'mailer_from');
                $replyTo = $this->get('fork.settings')->get('Core', 'mailer_reply_to');
                $message = Message::newInstance(
                    \SpoonFilter::ucfirst(BL::msg('ResetYourPasswordMailSubject'))
                )
                    ->setFrom([$from['email'] => $from['name']])
                    ->setTo([$email])
                    ->setReplyTo([$replyTo['email'] => $replyTo['name']])
                    ->parseHtml(
                        '/Authentication/Layout/Templates/Mails/ResetPassword.html.twig',
                        [
                            'resetLink' => SITE_URL . BackendModel::createUrlForAction('ResetPassword')
                                           . '&email=' . $email . '&key=' . $key,
                        ]
                    );
                $this->get('mailer')->send($message);

                // clear post-values
                $_POST['backend_email_forgot'] = '';

                // show success message
                $this->template->assign('isForgotPasswordSuccess', true);

                // show form
                $this->template->assign('showForm', true);
            } else {
                // errors?
                $this->template->assign('showForm', true);
            }
        }
    }

    /**
     * Find out which module and action are allowed
     * and send the user on his way.
     */
    private function redirectToAllowedModuleAndAction(): void
    {
        $allowedModule = $this->getAllowedModule();
        $allowedAction = $this->getAllowedAction($allowedModule);
        $allowedModuleActionUrl = $allowedModule !== false && $allowedAction !== false ?
            BackendModel::createUrlForAction($allowedAction, $allowedModule) :
            BackendModel::createUrlForAction('Index', 'Authentication');

        $userEmail = BackendAuthentication::getUser()->getEmail();
        $this->getContainer()->get('logger')->info(
            "Redirecting user '{$userEmail}' to {$allowedModuleActionUrl}."
        );

        $this->redirect(
            $this->getRequest()->query->get('querystring', $allowedModuleActionUrl)
        );
    }

    /**
     * Run through the action of a certain module and find us an action(name) this user is allowed to access.
     *
     * @param string $module
     *
     * @return bool|string
     */
    private function getAllowedAction(string $module)
    {
        if (BackendAuthentication::isAllowedAction('Index', $module)) {
            return 'Index';
        }
        $allowedAction = false;

        $groupsRightsActions = BackendUsersModel::getModuleGroupsRightsActions(
            $module
        );

        foreach ($groupsRightsActions as $groupsRightsAction) {
            $isAllowedAction = BackendAuthentication::isAllowedAction(
                $groupsRightsAction['action'],
                $module
            );
            if ($isAllowedAction) {
                $allowedAction = $groupsRightsAction['action'];
                break;
            }
        }

        return $allowedAction;
    }

    /**
     * Run through the modules and find us a module(name) this user is allowed to access.
     *
     * @return bool|string
     */
    private function getAllowedModule()
    {
        // create filter with modules which may not be displayed
        $filter = ['Authentication', 'Error', 'Core'];

        // get all modules
        $modules = array_diff(BackendModel::getModules(), $filter);
        $allowedModule = false;

        if (BackendAuthentication::isAllowedModule('Dashboard')) {
            $allowedModule = 'Dashboard';
        } else {
            foreach ($modules as $module) {
                if (BackendAuthentication::isAllowedModule($module)) {
                    $allowedModule = $module;
                    break;
                }
            }
        }

        return $allowedModule;
    }
}
