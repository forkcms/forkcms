<?php

namespace Common\Mailer;

/**
 * This class will create the right mailer transport based on some parameters
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class Transport
{
    /**
     * Create The right transport instance based on some settings
     *
     * @param  string $type
     * @param  string $server
     * @param  string $port
     * @param  string $user
     * @param  string $pass
     * @return \Swift_Transport
     */
    public static function newInstance($type = 'mail', $server = null, $port = 25, $user = null, $pass = null)
    {
        if ($type === 'smtp') {
            return self::getSmtpTransport($server, $port, $user, $pass);
        } else {
            return self::getMailTransport();
        }
    }

    /**
     * Create a new Smtp Mailer Transport instance.
     *
     * @param  string $server
     * @param  string $port
     * @param  string $user
     * @param  string $pass
     * @return \Swift_SmtpTransport
     */
    private static function getSmtpTransport($server, $port, $user, $pass)
    {
        return \Swift_SmtpTransport::newInstance($server, $port)
            ->setUsername($user)
            ->setPassword($pass)
        ;
    }


    /**
     * Create a new PHP Mailer Transport instance.
     *
     * @return \Swift_MailTransport
     */
    private static function getMailTransport()
    {
        return \Swift_MailTransport::newInstance();
    }
}
