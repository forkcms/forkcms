<?php

namespace Frontend\Modules\Mailmotor\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Language\Locale;
use Frontend\Modules\Mailmotor\Domain\Subscription\Command\Unsubscription;
use Frontend\Modules\Mailmotor\Domain\Subscription\Event\NotImplementedUnsubscribedEvent;
use Frontend\Modules\Mailmotor\Domain\Subscription\UnsubscribeType;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

/**
 * This is the Unsubscription-action for Mailmotor
 */
class Unsubscribe extends FrontendBaseBlock
{
    public function execute(): void
    {
        parent::execute();

        $form = $this->createForm(
            UnsubscribeType::class,
            new Unsubscription(Locale::frontendLanguage(), $this->getEmail())
        );

        $form->handleRequest($this->getRequest());

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->template->assign('form', $form->createView());

            if ($form->isSubmitted()) {
                $this->template->assign('mailmotorUnsubscribeHasFormError', true);
            }

            $this->loadTemplate();
            $this->parse();

            return;
        }

        /** @var Unsubscription $unsubscription */
        $unsubscription = $form->getData();

        try {
            // The command bus will handle the unsubscription
            $this->get('command_bus')->handle($unsubscription);
        } catch (NotImplementedException $e) {
            // fallback for when no mail-engine is chosen in the Backend
            $this->get('event_dispatcher')->dispatch(
                NotImplementedUnsubscribedEvent::EVENT_NAME,
                new NotImplementedUnsubscribedEvent($unsubscription)
            );
        }

        $this->redirect(
            FrontendNavigation::getUrlForBlock('Mailmotor', 'Unsubscribe')
            . '?unsubscribed=true'
            . '#mailmotorUnsubscribeForm'
        );
    }

    public function getEmail(): ?string
    {
        return $this->getRequest()->request->get('email');
    }

    private function parse(): void
    {
        if ($this->url->getParameter('unsubscribed') === 'true') {
            $this->template->assign('mailmotorUnsubscribeIsSuccess', true);
            $this->template->assign('mailmotorUnsubscribeHideForm', true);
        }
    }
}
