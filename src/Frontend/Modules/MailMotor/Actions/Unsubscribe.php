<?php

namespace Frontend\Modules\MailMotor\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\MailMotor\Engine\Model as FrontendMailMotorModel;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

/**
 * This is the Unsubscribe-action for MailMotor
 */
class Unsubscribe extends FrontendBaseBlock
{
    /**
     * FrontendForm instance
     *
     * @var	FrontendForm
     */
    private $frm;

    /**
     * Execute the extra
     *
     * @return void
     */
    public function execute()
    {
        $this->loadTemplate();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
    }

    /**
     * Load the form
     *
     * @return void
     */
    private function loadForm()
    {
        // create the form
        $this->frm = new FrontendForm(
            'mailMotorUnsubscribeForm',
            null,
            null,
            'mailMotorUnsubscribeForm'
        );

        // define email
        $email = null;

        // request contains an email
        if ($this->get('request')->request->get('email') != null) {
            $email = $this->get('request')->request->get('email');
        }

        // create & add elements
        $this->frm->addText('email', $email);
        $this->frm->addText('email')
            ->setAttributes(
                array(
                    'required' => null,
                    'type' => 'email',
                    'placeholder' => ucfirst(Language::lbl('YourEmail')),
                )
            )
        ;
    }

    /**
     * Parse the data into the template
     *
     * @return void
     */
    private function parse()
    {
        // form was sent?
        if ($this->URL->getParameter('sent') == 'true') {
            // show message
            $this->tpl->assign('mailMotorUnsubscribeIsSuccess', true);

            // hide form
            $this->tpl->assign('mailMotorUnsubscribeHideForm', true);
        }

        // parse the form
        $this->frm->parse($this->tpl);
    }

    /**
     * Validate the form
     *
     * @return void
     */
    private function validateForm()
    {
        // is the form submitted
        if ($this->frm->isSubmitted()) {
            // get values
            $email = $this->frm->getField('email');

            // validate required fields
            if ($email->isEmail(Language::err('EmailIsInvalid'))) {
                try {
                    // email exists
                    if ($this->get('mailmotor.subscriber')->exists($email->getValue())) {
                        // user is already unsubscribed
                        if ($this->get('mailmotor.subscriber')->isUnsubscribed($email->getValue())) {
                            $email->addError(Language::err('AlreadyUnsubscribed'));

                            // do not remove this line, it is required to make the form show error messages properly
                            $this->frm->addError(Language::err('AlreadyUnsubscribed'));
                        }
                    // email not exists
                    } else {
                        $email->addError(Language::err('EmailNotInDatabase'));

                        // do not remove this line, it is required to make the form show error messages properly
                        $this->frm->addError(Language::err('EmailNotInDatabase'));
                    }
                // fallback for when no mail-engine is chosen in the Backend
                } catch (NotImplementedException $e) {
                    // do nothing
                }
            // we need to add this because the line below
            // $this->frm->getErrors() only checks if form errors are set, not if an element in the form has errors.
            } else {
                $this->frm->addError(Language::err('EmailIsInvalid'));
            }

            // no errors
            if (trim($this->frm->getErrors()) == '') {
                try {
                    // unsubscribe the user
                    $this->get('mailmotor.subscriber')->unsubscribe(
                        $email->getValue()
                    );
                // fallback for when no mail-engine is chosen in the Backend
                } catch (NotImplementedException $e) {
                    // mail admin instead
                    $this->get('mailmotor.not_implemented.subscriber.mailer')->unsubscribe(
                        $email->getValue(),
                        FRONTEND_LANGUAGE
                    );
                }

                // redirect
                $this->redirect(
                    FrontendNavigation::getURLForBlock(
                        'MailMotor',
                        'Unsubscribe'
                    )
                    . '?sent=true'
                    . '#mailMotorUnsubscribeForm'
                );
            // show errors
            } else {
                $this->tpl->assign('mailMotorUnsubscribeHasFormError', true);
            }
        }
    }
}
