<?php

namespace Frontend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Mailer\Message;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;

/**
 * Request a reset password email.
 */
class ForgotPassword extends FrontendBaseBlock
{
    /**
     * FrontendForm instance.
     *
     * @var FrontendForm
     */
    private $form;

    public function execute(): void
    {
        // only for guests
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            parent::execute();
            $this->loadTemplate();
            $this->buildForm();
            $this->validateForm();
            $this->parse();
        } else {
            // already logged in, redirect to settings
            $this->redirect(FrontendNavigation::getUrlForBlock('Profiles', 'Settings'));
        }
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('forgotPassword', null, null, 'forgotPasswordForm');
        $this->form->addText('email')->setAttributes(['required' => null, 'type' => 'email']);
    }

    private function parse(): void
    {
        // e-mail was sent?
        if ($this->url->getParameter('sent') == 'true') {
            // show message
            $this->template->assign('forgotPasswordSuccess', true);

            // hide form
            $this->template->assign('forgotPasswordHideForm', true);
        }

        // parse the form
        $this->form->parse($this->template);
    }

    private function validateForm(): void
    {
        // is the form submitted
        if ($this->form->isSubmitted()) {
            // get field
            $txtEmail = $this->form->getField('email');

            // field is filled in?
            if ($txtEmail->isFilled(FL::getError('EmailIsRequired'))) {
                // valid email?
                if ($txtEmail->isEmail(FL::getError('EmailIsInvalid'))) {
                    // email exists?
                    if (!FrontendProfilesModel::existsByEmail($txtEmail->getValue())) {
                        $txtEmail->addError(FL::getError('EmailIsUnknown'));
                    }
                }
            }

            // valid login
            if ($this->form->isCorrect()) {
                // get profile id
                $profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());

                // generate forgot password key
                $key = FrontendProfilesModel::getEncryptedString(
                    $profileId . microtime(),
                    FrontendProfilesModel::getRandomString()
                );

                // insert forgot password key
                FrontendProfilesModel::setSetting($profileId, 'forgot_password_key', $key);

                // send email
                $from = $this->get('fork.settings')->get('Core', 'mailer_from');
                $replyTo = $this->get('fork.settings')->get('Core', 'mailer_reply_to');
                $message = Message::newInstance(FL::getMessage('ForgotPasswordSubject'))
                    ->setFrom([$from['email'] => $from['name']])
                    ->setTo([$txtEmail->getValue() => ''])
                    ->setReplyTo([$replyTo['email'] => $replyTo['name']])
                    ->parseHtml(
                        '/Profiles/Layout/Templates/Mails/ForgotPassword.html.twig',
                        [
                            'resetUrl' => SITE_URL . FrontendNavigation::getUrlForBlock(
                                'Profiles',
                                'ResetPassword'
                            ) . '/' . $key,
                            'firstName' => FrontendProfilesModel::getSetting($profileId, 'first_name'),
                            'lastName' => FrontendProfilesModel::getSetting($profileId, 'last_name'),
                        ],
                        true
                    )
                ;
                $this->get('mailer')->send($message);

                // redirect
                $this->redirect(SITE_URL . $this->url->getQueryString() . '?sent=true');
            } else {
                $this->template->assign('forgotPasswordHasError', true);
            }
        }
    }
}
