<?php

namespace Frontend\Modules\Mailmotor\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Mailmotor\Engine\Model as FrontendMailmotorModel;
use Frontend\Modules\Mailmotor\Engine\CMHelper as FrontendMailmotorCMHelper;

/**
 * This is the subscribe-action
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Subscribe extends FrontendBaseBlock
{
    /**
     * FrontendForm instance
     *
     * @var    FrontendForm
     */
    private $frm;

    /**
     * Execute the extra
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
     */
    private function loadForm()
    {
        // create the form
        $this->frm = new FrontendForm('subscribe', null, null, 'subscribeForm');

        // create & add elements
        $this->frm->addText('email')->setAttributes(array('required' => null, 'type' => 'email'));
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // form was sent?
        if ($this->URL->getParameter('sent') == 'true') {
            // show message
            $this->tpl->assign('subscribeIsSuccess', true);

            // hide form
            $this->tpl->assign('subscribeHideForm', true);
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
            // validate required fields
            $email = $this->frm->getField('email');

            // validate required fields
            if ($email->isEmail(FL::err('EmailIsInvalid'))) {
                if (FrontendMailmotorModel::isSubscribed($email->getValue())) {
                    $email->addError(
                        FL::err('AlreadySubscribed')
                    );
                }
            }

            // no errors
            if ($this->frm->isCorrect()) {
                try {
                    // subscribe the user to our default group
                    if (!FrontendMailmotorCMHelper::subscribe(
                        $email->getValue()
                    )
                    ) {
                        throw new FrontendException('Could not subscribe');
                    }

                    // trigger event
                    FrontendModel::triggerEvent('Mailmotor', 'after_subscribe', array('email' => $email->getValue()));

                    // redirect
                    $this->redirect(
                        FrontendNavigation::getURLForBlock('Mailmotor', 'Subscribe') . '?sent=true#subscribeForm'
                    );
                } catch (\Exception $e) {
                    // when debugging we need to see the exceptions
                    if (SPOON_DEBUG) {
                        throw $e;
                    }

                    // show error
                    $this->tpl->assign('subscribeHasError', true);
                }
            } else {
                $this->tpl->assign('subscribeHasFormError', true);
            }
        }
    }
}
