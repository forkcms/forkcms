<?php

namespace Frontend\Modules\Mailmotor\Actions;

use Exception;
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

        $form = $this->createForm(
            SubscribeType::class,
            new Subscription(Locale::frontendLanguage(), $this->getEmail())
        );

        $form->handleRequest($this->getRequest());

        if (!$form->isSubmitted() || !$form->isValid()) {
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

        /** @var bool $doubleOptIn */
        $doubleOptIn = $this->get('fork.settings')->get('Mailmotor', 'double_opt_in', false);

        try {
            // The command bus will handle the subscription
            $this->get('command_bus')->handle($subscription);
        } catch (NotImplementedException $e) {
            // fallback for when no mail-engine is chosen in the Backend
            $this->get('event_dispatcher')->dispatch(
                NotImplementedSubscribedEvent::EVENT_NAME,
                new NotImplementedSubscribedEvent($subscription)
            );

            $doubleOptIn = false;
        } catch (Exception $exception) {
            $reason = json_decode($exception->getMessage());
            // check if the error is one from mailchimp
            if ($reason === false) {
                throw $exception;
            }

            $this->getContainer()->get('logger')->error('Mailmotor Subcribe Mailchimp error: ' . $reason);

            $this->template->assign('mailmotorSubscribeHasFormError', true);
            $this->template->assign('form', $form->createView());

            $this->loadTemplate();
            $this->parse();

            return;
        }

        $redirectLink .= '&double-opt-in=';
        $redirectLink .= $doubleOptIn ? 'true' : 'false';
        $redirectLink .= '#mailmotorSubscribeForm';

        $this->redirect($redirectLink);
    }

    public function getEmail(): ?string
    {
        if ($this->getRequest()->request->get('email') !== null) {
            return $this->getRequest()->request->get('email');
        }

        return null;
    }

    private function parse(): void
    {
        if ($this->url->getParameter('subscribed') !== 'true') {
            return;
        }

        $this->template->assign('mailmotorSubscribeIsSuccess', true);
        $this->template->assign(
            'mailmotorSubscribeHasDoubleOptIn',
            $this->url->getParameter('double-opt-in', 'string', 'true') === 'true'
        );
        $this->template->assign('mailmotorSubscribeHideForm', true);
    }
}
