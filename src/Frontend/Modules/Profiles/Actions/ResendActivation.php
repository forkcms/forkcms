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
 * This is the resend activation-action. It will resend your activation email.
 */
class ResendActivation extends FrontendBaseBlock
{
    /**
     * @var FrontendForm
     */
    private $form;

    public function execute(): void
    {
        // profile not logged in
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            parent::execute();
            $this->loadTemplate();
            $this->buildForm();
            $this->validateForm();
            $this->parse();
        } else {
            // profile logged in
            $this->redirect(FrontendNavigation::getUrl(404));
        }
    }

    private function buildForm(): void
    {
        // create the form
        $this->form = new FrontendForm('resendActivation', null, null, 'resendActivation');

        // create & add elements
        $this->form->addText('email')->setAttributes(['required' => null, 'type' => 'email']);
    }

    private function parse(): void
    {
        // form was sent?
        if ($this->url->getParameter('sent') == 'true') {
            // show message
            $this->template->assign('resendActivationSuccess', true);

            // hide form
            $this->template->assign('resendActivationHideForm', true);
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
                    if (FrontendProfilesModel::existsByEmail($txtEmail->getValue())) {
                        // get profile id using the filled in email
                        $profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());

                        // get profile
                        $profile = FrontendProfilesModel::get($profileId);

                        // must be inactive
                        if ($profile->getStatus() != FrontendProfilesAuthentication::LOGIN_INACTIVE
                        ) {
                            $txtEmail->addError(FL::getError('ProfileIsActive'));
                        }
                    } else {
                        // email don't exist
                        $txtEmail->addError(FL::getError('EmailIsInvalid'));
                    }
                }
            }

            // valid login
            if ($this->form->isCorrect()) {
                // send email
                $from = $this->get('fork.settings')->get('Core', 'mailer_from');
                $replyTo = $this->get('fork.settings')->get('Core', 'mailer_reply_to');
                $message = Message::newInstance(FL::getMessage('RegisterSubject'))
                    ->setFrom([$from['email'] => $from['name']])
                    ->setTo([$profile->getEmail() => ''])
                    ->setReplyTo([$replyTo['email'] => $replyTo['name']])
                    ->parseHtml(
                        '/Profiles/Layout/Templates/Mails/Register.html.twig',
                        [
                            'activationUrl' => SITE_URL . FrontendNavigation::getUrlForBlock('Profiles', 'Activate') .
                                               '/' . $profile->getSetting('activation_key'),
                        ],
                        true
                    )
                ;
                $this->get('mailer')->send($message);

                // redirect
                $this->redirect(SITE_URL . $this->url->getQueryString() . '?sent=true');
            } else {
                $this->template->assign('resendActivationHasError', true);
            }
        }
    }
}
