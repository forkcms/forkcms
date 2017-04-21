<?php

namespace Common\Mailer;

use Swift_MailTransport;
use Swift_SmtpTransport;
use Swift_Transport;

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
     * @return Swift_Transport
     */
    public static function create(
        string $type = 'mail',
        string $server = null,
        int $port = 25,
        string $user = null,
        string $pass = null,
        string $encryption = null
    ): Swift_Transport {
        if ($type === 'smtp') {
            return self::getSmtpTransport($server, $port, $user, $pass, $encryption);
        }

        return self::getMailTransport();
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
     * @return Swift_SmtpTransport
     */
    private static function getSmtpTransport(
        string $server,
        string $port,
        string $user,
        string $pass,
        string $encryption = null
    ): Swift_SmtpTransport {
        $transport = Swift_SmtpTransport::newInstance($server, $port)
            ->setUsername($user)
            ->setPassword($pass);

        if (in_array($encryption, ['ssl', 'tls'])) {
            $transport->setEncryption($encryption);
        }

        return $transport;
    }

    /**
     * Create a new PHP Mailer Transport instance.
     *
     * @return Swift_MailTransport
     */
    private static function getMailTransport(): Swift_MailTransport
    {
        return Swift_MailTransport::newInstance();
    }
}
