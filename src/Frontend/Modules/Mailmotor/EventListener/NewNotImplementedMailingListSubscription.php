<?php

namespace Frontend\Modules\Mailmotor\EventListener;

use Common\Language;
use Common\Mailer\Message;
use Frontend\Modules\Mailmotor\Domain\Subscription\Event\NotImplementedSubscribedEvent;
use Swift_Mailer;
use Common\ModulesSettings;

/**
 * New mailing list subscription
 *
 * This will send a mail to the administrator
 * to let them know that they have to manually subscribe a person.
 * Because the mail engine is "not_implemented".
 */
final class NewNotImplementedMailingListSubscription
{
    /**
     * @var ModulesSettings
     */
    private $modulesSettings;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    public function __construct(Swift_Mailer $mailer, ModulesSettings $modulesSettings)
    {
        $this->mailer = $mailer;
        $this->modulesSettings = $modulesSettings;
    }

    public function onNotImplementedSubscribedEvent(NotImplementedSubscribedEvent $event): void
    {
        $title = sprintf(
            Language::lbl('MailTitleSubscribeSubscriber'),
            $event->getSubscription()->email,
            strtoupper((string) $event->getSubscription()->locale)
        );

        $to = $this->modulesSettings->get('Core', 'mailer_to');
        $from = $this->modulesSettings->get('Core', 'mailer_from');
        $replyTo = $this->modulesSettings->get('Core', 'mailer_reply_to');

        $message = Message::newInstance($title)
            ->setFrom([$from['email'] => $from['name']])
            ->setTo([$to['email'] => $to['name']])
            ->setReplyTo([$replyTo['email'] => $replyTo['name']])
            ->parseHtml(
                FRONTEND_CORE_PATH . '/Layout/Templates/Mails/Notification.html.twig',
                [
                    'message' => $title,
                ],
                true
            )
        ;

        $this->mailer->send($message);
    }
}
