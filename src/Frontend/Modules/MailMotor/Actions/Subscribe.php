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
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\MailMotor\Engine\Model as FrontendMailMotorModel;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

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
     * @var array
     */
    private $interests;

    /**
     * Execute the extra
     *
     * @return void
     */
    public function execute()
    {
        parent::execute();
        $this->loadData();
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
    private function loadData()
    {
        try {
            // Define interests
            $this->interests = $this->get('mailmotor.subscriber')->getInterests();
        // Fallback for when no mail-engine is chosen in the Backend
        } catch (NotImplementedException $e) {
            $this->interests = false;
        }
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

        // Has interests
        if ($this->interests) {
            // Init
            $chkInterestValues = array();

            // Loop interests
            foreach ($this->interests as $categoryId => $categoryInterest) {
                foreach ($categoryInterest['children'] as $categoryChildId => $categoryChildTitle) {
                    // Add interest value for checkbox
                    $chkInterestValues[] = [
                        'value' => $categoryChildId,
                        'label' => $categoryChildTitle,
                    ];
                }
            }

            $this->frm->addMultiCheckbox(
                'interests',
                $chkInterestValues
            );
        }
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
            $this->tpl->assign('mailMotorSubscribeHasDoubleOptIn', ($this->URL->getParameter('double-opt-in', 'string', 'true') === 'true'));

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
            $interests = array();

            // validate required fields
            if ($email->isEmail(Language::err('EmailIsInvalid'))) {
                try {
                    if ($this->get('mailmotor.subscriber')->isSubscribed($email->getValue())) {
                        $email->addError(Language::err('AlreadySubscribed'));

                        // Do not remove this line, it is required to make the form show error messages properly
                        $this->frm->addError(Language::err('AlreadySubscribed'));
                    }
                // Fallback for when no mail-engine is chosen in the Backend
                } catch (NotImplementedException $e) {
                    // do nothing
                }
            // We need to add this because the line below
            // $this->frm->getErrors() only checks if form errors are set, not if an element in the form has errors.
            } else {
                $this->frm->addError(Language::err('EmailIsInvalid'));
            }

            // Has interests
            if ($this->interests) {
                // Define interest field
                $interestsField = $this->frm->getField('interests');

                // One interests needs to be filled
                if (!$interestsField->isFilled(Language::err('InterestsIsRequired'))) {
                    // Do not remove this line, it is required to make the form show error messages properly
                    $this->frm->addError(Language::err('InterestsIsRequired'));
                }

                // Define overwrite interests
                $overwriteInterests = $this->get('fork.settings')->get('MailMotor', 'overwrite_interests', true);

                // Redefine interests
                $checkedInterests = $interestsField->getChecked();

                // We must overwrite existing interests
                if ($overwriteInterests) {
                    // Loop interests
                    foreach ($this->interests as $categoryId => $categoryInterest) {
                        foreach ($categoryInterest['children'] as $categoryChildId => $categoryChildTitle) {
                            // Add interest
                            $interests[$categoryChildId] = in_array($categoryChildId, $checkedInterests);
                        }
                    }
                } else {
                    // Loop checked interests
                    foreach ($checkedInterests as $checkedInterestId) {
                        // Add interest
                        $interests[$checkedInterestId] = true;
                    }
                }
            }

            // No errors
            if (trim($this->frm->getErrors()) == '') {
                // Init redirect link
                $redirectLink = FrontendNavigation::getURLForBlock(
                    'MailMotor',
                    'Subscribe'
                )
                . '?sent=true';

                try {
                    // Subscribe the user to our default group
                    $this->get('mailmotor.subscriber')->subscribe(
                        $email->getValue(),
                        FRONTEND_LANGUAGE,
                        $mergeFields,
                        $interests
                    );
                // Fallback for when no mail-engine is chosen in the Backend
                } catch (NotImplementedException $e) {
                    // Mail admin instead
                    FrontendMailMotorModel::mailAdminToSubscribeSubscriber(
                        $email->getValue(),
                        FRONTEND_LANGUAGE
                    );

                    $redirectLink .= '&double-opt-in=false';
                }

                // Redirect
                $this->redirect(
                    $redirectLink
                    . '#mailMotorSubscribeForm'
                );
            // show errors
            } else {
                $this->tpl->assign('mailMotorSubscribeHasFormError', true);
            }
        }
    }
}
