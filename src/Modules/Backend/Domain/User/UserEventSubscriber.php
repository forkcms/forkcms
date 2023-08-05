<?php

namespace ForkCMS\Modules\Backend\Domain\User;

use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class UserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onAuthenticationSuccess',
            LoginFailureEvent::class => 'onAuthenticationFailure',
        ];
    }

    public function onAuthenticationSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $user->registerAuthenticationSuccess();
        $this->userRepository->save($user);

        $session = $event->getRequest()->getSession();
        $session->set('user_locale', $user->getSetting('locale'));
    }

    public function onAuthenticationFailure(LoginFailureEvent $event): void
    {
        try {
            $user = $event->getPassport()?->getUser();
        } catch (LogicException) {
            return;
        }

        if (!$user instanceof User) {
            return;
        }

        $user->registerAuthenticationFailure();
        $this->userRepository->save($user);
    }
}
