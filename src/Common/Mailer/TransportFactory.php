<?php

namespace Common\Mailer;

/**
 * This class will create the right mailer transport based on some parameters
 */
class TransportFactory
{
    /**
     * Create The right transport instance based on some settings
     *
     * @param  string $type
     * @param  string $server
     * @param  int $port
     * @param  string $user
     * @param  string $pass
     * @param  string $encryption
     *
     * @return \Swift_Transport
     */
    public static function create($type = 'mail', $server = null, $port = 25, $user = null, $pass = null, $encryption = null)
    {
        if ($type === 'smtp') {
            return self::getSmtpTransport($server, $port, $user, $pass, $encryption);
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
     * @param  string $encryption
     *
     * @return \Swift_SmtpTransport
     */
    private static function getSmtpTransport($server, $port, $user, $pass, $encryption = null)
    {
        $transport = \Swift_SmtpTransport::newInstance($server, $port)
            ->setUsername($user)
            ->setPassword($pass)
        ;

        if (in_array($encryption, array('ssl', 'tls'))) {
            $transport->setEncryption($encryption);
        }

        return $transport;
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
