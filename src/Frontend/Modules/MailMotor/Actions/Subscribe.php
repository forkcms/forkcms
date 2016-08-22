<?php

namespace Frontend\Modules\MailMotor\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\MailMotor\Engine\Model as FrontendMailMotorModel;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

/**
 * This is the Subscribe-action for our MailMotor
 */
class Subscribe extends FrontendBaseBlock
{
    /**
     * FrontendForm instance
     *
     * @var FrontendForm
     */
    private $form;

    /**
     * Execute the extra
     *
     * @return void
     */
    public function execute()
    {
        parent::execute();
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
        $this->form = new FrontendForm('mailMotorSubscribeForm');

        // define email, default = null
        $email = $this->get('request')->request->get('email');

        // create & add elements
        $this->form->addText('email', $email)
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
    protected function parse()
    {
        // form was sent?
        if ($this->URL->getParameter('sent') == 'true') {
            // show message
            $this->tpl->assign('mailMotorSubscribeIsSuccess', true);

            // hide form
            $this->tpl->assign('mailMotorSubscribeHideForm', true);
        }

        // parse the form
        $this->form->parse($this->tpl);
    }

    /**
     * Validate the form
     *
     * @return void
     */
    private function validateForm()
    {
        // is the form submitted
        if ($this->form->isSubmitted()) {
            // validate required fields
            $email = $this->form->getField('email');

            // validate required fields
            if ($email->isEmail(Language::err('EmailIsInvalid'))) {
                try {
                    if ($this->get('mailmotor.subscriber')->isSubscribed($email->getValue())) {
                        $email->addError(Language::err('AlreadySubscribed'));

                        // do not remove this line, it is required to make the form show error messages properly
                        $this->form->addError(Language::err('AlreadySubscribed'));
                    }
                // fallback for when no mail-engine is chosen in the Backend
                } catch (NotImplementedException $e) {
                    // do nothing
                }
            // we need to add this because the line below
            // $this->form->getErrors() only checks if form errors are set, not if an element in the form has errors.
            } else {
                $this->form->addError(Language::err('EmailIsInvalid'));
            }

            // no errors
            if (trim($this->form->getErrors()) == '') {
                try {
                    // subscribe the user to our default group
                    $this->get('mailmotor.subscriber')->subscribe(
                        $email->getValue(),
                        array(), // MergeFields are optional
                        FRONTEND_LANGUAGE
                    );
                // fallback for when no mail-engine is chosen in the Backend
                } catch (NotImplementedException $e) {
                    // mail admin instead
                    $this->get('mailmotor.not_implemented.subscriber.mailer')->subscribe(
                        $email->getValue(),
                        FRONTEND_LANGUAGE
                    );
                }

                // redirect
                $this->redirect(
                    FrontendNavigation::getURLForBlock(
                        'MailMotor',
                        'Subscribe'
                    )
                    . '?sent=true'
                    . '#mailMotorSubscribeForm'
                );
            // show errors
            } else {
                $this->tpl->assign('mailMotorSubscribeHasFormError', true);
            }
        }
    }
}
