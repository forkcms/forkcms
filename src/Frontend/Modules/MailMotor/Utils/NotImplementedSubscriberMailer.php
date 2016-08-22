<?php

namespace Frontend\Modules\MailMotor\Utils;

use Common\Mailer\Message;
use Swift_Mailer;
use Common\ModulesSettings;
use Frontend\Core\Engine\Language;

/**
 * No mail engine is set up, so we must send mail to the administrator
 * to notify him/her about a new subscribe/unsubscribe.
 */
class NotImplementedSubscriberMailer
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
     * Send mail
     *
     * @param string $titleLabel
     * @param string $email
     * @param string $language
     */
    protected function send(
        $titleLabel,
        $email,
        $language
    ) {
        // define title
        $title = sprintf(
            Language::lbl($titleLabel),
            $email,
            strtoupper($language)
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

    /**
     * Mail admin to subscribe the subscriber
     *
     * @param string $email
     * @param string $language
     */
    public function subscribe(
        $email,
        $language
    ) {
        // mail admin
        $this->send(
            'MailTitleSubscribeSubscriber',
            $email,
            $language
        );
    }

    /**
     * Mail admin to unsubscribe the subscriber
     *
     * @param string $email
     * @param string $language
     */
    public function unsubscribe(
        $email,
        $language
    ) {
        // mail admin
        $this->send(
            'MailTitleUnsubscribeSubscriber',
            $email,
            $language
        );
    }
}
