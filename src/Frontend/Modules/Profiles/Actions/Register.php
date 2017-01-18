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
use Common\Exception\RedirectException as RedirectException;

/**
 * Register a profile.
 */
class Register extends FrontendBaseBlock
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
        parent::execute();

        $this->loadTemplate();

        // profile not logged in
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            $this->loadForm();
            $this->validateForm();
            $this->parse();
        } elseif ($this->URL->getParameter('sent') == true) {
            // just registered so show success message
            $this->parse();
        } else {
            // already logged in, so you can not register
            $this->redirect(SITE_URL);
        }
    }

    /**
     * Load the form.
     */
    private function loadForm()
    {
        $this->frm = new FrontendForm('register', null, null, 'registerForm');
        $this->frm->addText('display_name');
        $this->frm->addText('email')->setAttributes(array('required' => null, 'type' => 'email'));
        $this->frm->addPassword('password', null, null, 'inputText showPasswordInput')->setAttributes(
            array('required' => null)
        );
        $this->frm->addCheckbox('show_password');
    }

    /**
     * Parse the data into the template.
     */
    private function parse()
    {
        // e-mail was sent?
        if ($this->URL->getParameter('sent') == 'true') {
            // show message
            $this->tpl->assign('registerIsSuccess', true);

            // hide form
            $this->tpl->assign('registerHideForm', true);
        } else {
            $this->frm->parse($this->tpl);
        }
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted
        if ($this->frm->isSubmitted()) {
            // get fields
            $txtDisplayName = $this->frm->getField('display_name');
            $txtEmail = $this->frm->getField('email');
            $txtPassword = $this->frm->getField('password');

            // check email
            if ($txtEmail->isFilled(FL::getError('EmailIsRequired'))) {
                // valid email?
                if ($txtEmail->isEmail(FL::getError('EmailIsInvalid'))) {
                    // email already exists?
                    if (FrontendProfilesModel::existsByEmail($txtEmail->getValue())) {
                        // set error
                        $txtEmail->setError(FL::getError('EmailExists'));
                    }
                }
            }

            // check password
            $txtPassword->isFilled(FL::getError('PasswordIsRequired'));
            $txtDisplayName->isFilled(FL::getError('FieldIsRequired'));

            // no errors
            if ($this->frm->isCorrect()) {
                // init values
                $settings = array();
                $values = array();

                // generate salt
                $settings['salt'] = FrontendProfilesModel::getRandomString();
                $settings['language'] = LANGUAGE;

                // values
                $values['email'] = $txtEmail->getValue();
                $values['password'] = FrontendProfilesModel::getEncryptedString(
                    $txtPassword->getValue(),
                    $settings['salt']
                );
                $values['status'] = 'inactive';
                $values['display_name'] = $txtDisplayName->getValue();
                $values['registered_on'] = FrontendModel::getUTCDate();
                $values['last_login'] = FrontendModel::getUTCDate(null, 0);

                /*
                 * Add a profile.
                 * We use a try-catch statement to catch errors when more users sign up simultaneously.
                 */
                try {
                    // insert profile
                    $profileId = FrontendProfilesModel::insert($values);

                    // use the profile id as url until we have an actual url
                    FrontendProfilesModel::update(
                        $profileId,
                        array('url' => FrontendProfilesModel::getUrl($values['display_name']))
                    );

                    // generate activation key
                    $settings['activation_key'] = FrontendProfilesModel::getEncryptedString(
                        $profileId . microtime(),
                        $settings['salt']
                    );

                    // set settings
                    FrontendProfilesModel::setSettings($profileId, $settings);

                    // login
                    FrontendProfilesAuthentication::login($profileId);

                    // activation URL
                    $mailValues['activationUrl'] = SITE_URL . FrontendNavigation::getURLForBlock('Profiles', 'Activate')
                                                   . '/' . $settings['activation_key'];

                    // send email
                    $from = $this->get('fork.settings')->get('Core', 'mailer_from');
                    $replyTo = $this->get('fork.settings')->get('Core', 'mailer_reply_to');
                    $message = Message::newInstance(FL::getMessage('RegisterSubject'))
                        ->setFrom(array($from['email'] => $from['name']))
                        ->setTo(array($txtEmail->getValue() => ''))
                        ->setReplyTo(array($replyTo['email'] => $replyTo['name']))
                        ->parseHtml(
                            '/Profiles/Layout/Templates/Mails/Register.html.twig',
                            $mailValues,
                            true
                        )
                    ;
                    $this->get('mailer')->send($message);

                    // redirect
                    $this->redirect(SITE_URL . $this->URL->getQueryString() . '?sent=true');
                } catch (\Exception $e) {
                    // make sure RedirectExceptions get thrown
                    if ($e instanceof RedirectException) {
                        throw $e;
                    }

                    // when debugging we need to see the exceptions
                    if ($this->getContainer()->getParameter('kernel.debug')) {
                        throw $e;
                    }

                    // show error
                    $this->tpl->assign('registerHasFormError', true);
                }
            } else {
                $this->tpl->assign('registerHasFormError', true);
            }
        }
    }
}
