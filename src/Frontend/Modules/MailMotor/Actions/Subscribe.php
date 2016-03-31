<?php

namespace Frontend\Modules\MailMotor\Actions;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\MailMotor\Engine\Model as FrontendMailMotorModel;
use MailMotor\Bundle\MailMotorBundle\Component\Exception\NotImplementedException;

/**
 * This is the Subscribe-action for our MailMotor
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class Subscribe extends FrontendBaseBlock
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
        $this->frm = new FrontendForm('mailMotorSubscribeForm');

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
            // validate required fields
            $email = $this->frm->getField('email');

            // build
            $mergeFields = array();

            // validate required fields
            if ($email->isEmail(Language::err('EmailIsInvalid'))) {
                try {
                    if (FrontendModel::get('mailmotor.subscriber')->isSubscribed($email->getValue())) {
                        $email->addError(Language::err('AlreadySubscribed'));

                        // do not remove this line, it is required to make the form show error messages properly
                        $this->frm->addError(Language::err('AlreadySubscribed'));
                    }
                // no mail-engine is chosen in the Backend,
                // so we have this fallback to send a mail to the admin
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
                    // subscribe the user to our default group
                    FrontendModel::get('mailmotor.subscriber')->subscribe(
                        $email->getValue(),
                        null,
                        $mergeFields,
                        FRONTEND_LANGUAGE
                    );
                // no mail-engine is chosen in the Backend,
                // so we have this fallback to send a mail to the admin
                } catch (NotImplementedException $e) {
                    // mail admin
                    FrontendMailMotorModel::mailAdminToSubscribeSubscriber(
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
