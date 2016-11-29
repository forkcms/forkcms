<?php

namespace Frontend\Modules\MailMotor\Actions;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\MailMotor\Command\Subscription;
use Frontend\Modules\MailMotor\Event\NotImplementedSubscribedEvent;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

/**
 * This is the Subscribe-action for the MailMotor
 */
class Subscribe extends FrontendBaseBlock
{
    /**
     * Execute the extra
     *
     * @return void
     */
    public function execute()
    {
        parent::execute();

        // Define email from the subscribe widget
        $email = $this->getEmail();

        // Create the form
        $form = $this->createForm(
            $this->get('mailmotor.form.subscription'),
            new Subscription(
                $email,
                FRONTEND_LANGUAGE
            )
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

            if ($form->isSubmitted()) {
                $this->tpl->assign('mailMotorSubscribeHasFormError', true);
            }

            $this->loadTemplate();
            $this->parse();

            return;
        }

        $redirectLink = FrontendNavigation::getURLForBlock(
            'MailMotor',
            'Subscribe'
        ) . '?subscribed=true';

        /** @var Subscription $subscription */
        $subscription = $form->getData();

        try {
            // The command bus will handle the unsubscription
            $this->get('command_bus')->handle($subscription);
        } catch (NotImplementedException $e) {
            // fallback for when no mail-engine is chosen in the Backend
            $this->get('event_dispatcher')->dispatch(
                NotImplementedSubscribedEvent::EVENT_NAME,
                new NotImplementedSubscribedEvent(
                    $subscription
                )
            );

            $redirectLink .= '&double-opt-in=false';
        }

        $redirectLink .= '#mailMotorSubscriptionForm';

        return $this->redirect($redirectLink);
    }

    /**
     * Get email
     */
    public function getEmail()
    {
        // define email
        $email = null;

        // request contains an email
        if ($this->get('request')->request->get('email') != null) {
            $email = $this->get('request')->request->get('email');
        }

        return $email;
    }

    /**
     * Parse the data into the template
     *
     * @return void
     */
    private function parse()
    {
        // form was subscribed?
        if ($this->URL->getParameter('subscribed') == 'true') {
            // show message
            $this->tpl->assign('mailMotorSubscribeIsSuccess', true);
            $this->tpl->assign('mailMotorSubscribeHasDoubleOptIn', ($this->URL->getParameter('double-opt-in', 'string', 'true') === 'true'));

            // hide form
            $this->tpl->assign('mailMotorSubscribeHideForm', true);
        }
    }
}
