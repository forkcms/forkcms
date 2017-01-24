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
use Frontend\Core\Engine\Model as FrontendModel;
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
    private $frm;

    /**
     * Execute the extra.
     */
    public function execute()
    {
        // only for guests
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            parent::execute();
            $this->loadTemplate();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
        } else {
            // already logged in, redirect to settings
            $this->redirect(FrontendNavigation::getURLForBlock('Profiles', 'Settings'));
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new FrontendForm('forgotPassword', null, null, 'forgotPasswordForm');
        $this->frm->addText('email')->setAttributes(array('required' => null, 'type' => 'email'));
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // e-mail was sent?
        if ($this->URL->getParameter('sent') == 'true') {
            // show message
            $this->tpl->assign('forgotPasswordSuccess', true);

            // hide form
            $this->tpl->assign('forgotPasswordHideForm', true);
        }

        // parse the form
        $this->frm->parse($this->tpl);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted
        if ($this->frm->isSubmitted()) {
            // get field
            $txtEmail = $this->frm->getField('email');

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
            if ($this->frm->isCorrect()) {
                // get profile id
                $profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());

                // generate forgot password key
                $key = FrontendProfilesModel::getEncryptedString(
                    $profileId . microtime(),
                    FrontendProfilesModel::getRandomString()
                );

                // insert forgot password key
                FrontendProfilesModel::setSetting($profileId, 'forgot_password_key', $key);

                // reset url
                $mailValues['resetUrl'] = SITE_URL . FrontendNavigation::getURLForBlock('Profiles', 'ResetPassword') .
                                          '/' . $key;
                $mailValues['firstName'] = FrontendProfilesModel::getSetting($profileId, 'first_name');
                $mailValues['lastName'] = FrontendProfilesModel::getSetting($profileId, 'last_name');

                // send email
                $from = $this->get('fork.settings')->get('Core', 'mailer_from');
                $replyTo = $this->get('fork.settings')->get('Core', 'mailer_reply_to');
                $message = Message::newInstance(FL::getMessage('ForgotPasswordSubject'))
                    ->setFrom(array($from['email'] => $from['name']))
                    ->setTo(array($txtEmail->getValue() => ''))
                    ->setReplyTo(array($replyTo['email'] => $replyTo['name']))
                    ->parseHtml(
                        '/Profiles/Layout/Templates/Mails/ForgotPassword.html.twig',
                        $mailValues,
                        true
                    )
                ;
                $this->get('mailer')->send($message);

                // redirect
                $this->redirect(SITE_URL . $this->URL->getQueryString() . '?sent=true');
            } else {
                $this->tpl->assign('forgotPasswordHasError', true);
            }
        }
    }
}
