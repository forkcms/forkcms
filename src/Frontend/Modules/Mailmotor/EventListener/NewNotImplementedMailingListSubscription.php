<?php

namespace Frontend\Modules\Mailmotor\EventListener;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Language;
use Common\Mailer\Message;
use Frontend\Modules\Mailmotor\Event\NotImplementedSubscribedEvent;
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
    protected $modulesSettings;

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @param Swift_Mailer $mailer
     * @param ModulesSettings $modulesSettings
     */
    public function __construct(
        Swift_Mailer $mailer,
        ModulesSettings $modulesSettings
    ) {
        $this->mailer = $mailer;
        $this->modulesSettings = $modulesSettings;
    }

    /**
     * On NotImplementedSubscribedEvent
     *
     * @param NotImplementedSubscribedEvent $event
     */
    public function onNotImplementedSubscribedEvent(
        NotImplementedSubscribedEvent $event
    ) {
        // define title
        $title = sprintf(
            Language::lbl('MailTitleSubscribeSubscriber'),
            $event->getSubscription()->email,
            strtoupper($event->getSubscription()->language)
        );

        // define sender/receiver(s)
        $to = $this->modulesSettings->get('Core', 'mailer_to');
        $from = $this->modulesSettings->get('Core', 'mailer_from');
        $replyTo = $this->modulesSettings->get('Core', 'mailer_reply_to');

        // define message
        $message = Message::newInstance($title)
            ->setFrom(array($from['email'] => $from['name']))
            ->setTo(array($to['email'] => $to['name']))
            ->setReplyTo(array($replyTo['email'] => $replyTo['name']))
            ->parseHtml(
                FRONTEND_CORE_PATH . '/Layout/Templates/Mails/Notification.html.twig',
                array(
                    'message' => $title
                ),
                true
            )
        ;

        // send mail
        $this->mailer->send($message);
    }
}
