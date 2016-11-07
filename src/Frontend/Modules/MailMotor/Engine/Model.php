<?php

namespace Frontend\Modules\MailMotor\Engine;

use Frontend\Core\Engine\Language;
use Frontend\Core\Engine\Model as FrontendModel;

/**
 * In this file we store all generic functions that we will be using in the MailMotor module
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class Model
{
    /**
     * Mail admin
     *
     * @param string $title
     * @param string $email
     * @param string $language
     */
    protected static function mailAdmin(
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
        $to = FrontendModel::get('fork.settings')->get('Core', 'mailer_to');
        $from = FrontendModel::get('fork.settings')->get('Core', 'mailer_from');
        $replyTo = FrontendModel::get('fork.settings')->get('Core', 'mailer_reply_to');

        // define message
        $message = \Common\Mailer\Message::newInstance($title)
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
        FrontendModel::get('mailer')->send($message);
    }

    /**
     * Mail admin to subscribe member
     *
     * @param string $email
     */
    public static function mailAdminToSubscribeSubscriber(
        $email,
        $language
    ) {
        // mail admin
        self::mailAdmin(
            'MailTitleSubscribeSubscriber',
            $email,
            $language
        );
    }

    /**
     * Mail admin to unsubscribe member
     *
     * @param string $email
     */
    public static function mailAdminToUnsubscribeSubscriber(
        $email,
        $language
    ) {
        // mail admin
        self::mailAdmin(
            'MailTitleUnsubscribeSubscriber',
            $email,
            $language
        );
    }
}
