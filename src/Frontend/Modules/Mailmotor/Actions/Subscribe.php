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
use Frontend\Modules\Mailmotor\Domain\Subscription\Command\Subscription;
use Frontend\Modules\Mailmotor\Domain\Subscription\Event\NotImplementedSubscribedEvent;
use Frontend\Modules\Mailmotor\Domain\Subscription\SubscribeType;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

/**
 * This is the Subscribe-action for the Mailmotor
 */
class Subscribe extends FrontendBaseBlock
{
    public function execute(): void
    {
        parent::execute();

        // Define email from the subscribe widget
        $email = $this->getEmail();

        // Create the form
        $form = $this->createForm(
            SubscribeType::class,
            new Subscription(
                Locale::frontendLanguage(),
                $email
            )
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->template->assign('form', $form->createView());

            if ($form->isSubmitted()) {
                $this->template->assign('mailmotorSubscribeHasFormError', true);
            }

            $this->loadTemplate();
            $this->parse();

            return;
        }

        $redirectLink = FrontendNavigation::getUrlForBlock(
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

        $this->redirect($redirectLink);
    }

    public function getEmail(): ?string
    {
        // define email
        $email = null;

        // request contains an email
        if ($this->get('request')->request->get('email') != null) {
            $email = $this->get('request')->request->get('email');
        }

        return $email;
    }

    private function parse(): void
    {
        // form was subscribed?
        if ($this->url->getParameter('subscribed') == 'true') {
            // show message
            $this->template->assign('mailmotorSubscribeIsSuccess', true);
            $this->template->assign('mailmotorSubscribeHasDoubleOptIn', ($this->url->getParameter('double-opt-in', 'string', 'true') === 'true'));

            // hide form
            $this->template->assign('mailmotorSubscribeHideForm', true);
        }
    }
}
