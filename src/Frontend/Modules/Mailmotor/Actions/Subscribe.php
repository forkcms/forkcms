<?php

namespace Frontend\Modules\Mailmotor\Actions;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Language\Locale;
use Frontend\Modules\Mailmotor\Command\Subscription;
use Frontend\Modules\Mailmotor\Event\NotImplementedSubscribedEvent;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

/**
 * This is the Subscribe-action for the Mailmotor
 */
class Subscribe extends FrontendBaseBlock
{
    /**
     * Execute the extra
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
                Locale::frontendLanguage(),
                $email
            )
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

            if ($form->isSubmitted()) {
                $this->tpl->assign('mailmotorSubscribeHasFormError', true);
            }

            $this->loadTemplate();
            $this->parse();

            return;
        }

        $redirectLink = FrontendNavigation::getURLForBlock(
            'Mailmotor',
            'Subscribe'
        ) . '?subscribed=true';

        /** @var Subscription $subscription */
        $subscription = $form->getData();

        /** @var bool $doubleOptin */
        $doubleOptin = $this->get('fork.settings')->get('Mailmotor', 'double_opt_in', false);

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

            $doubleOptin = false;
        }

        $redirectLink .= '&double-opt-in=';
        $redirectLink .= $doubleOptin ? 'true' : 'false';
        $redirectLink .= '#mailmotorSubscribeForm';

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
     */
    private function parse()
    {
        // form was subscribed?
        if ($this->URL->getParameter('subscribed') == 'true') {
            // show message
            $this->tpl->assign('mailmotorSubscribeIsSuccess', true);
            $this->tpl->assign('mailmotorSubscribeHasDoubleOptIn', ($this->URL->getParameter('double-opt-in', 'string', 'true') === 'true'));

            // hide form
            $this->tpl->assign('mailmotorSubscribeHideForm', true);
        }
    }
}
