<?php

namespace Common\Mailer;

use Frontend\Core\Engine\Model;

/**
 * This class will send mails
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class Transport implements \Swift_Transport
{
    private $transport;

    /**
     * Create a new Transport instance
     */
    public function __construct()
    {
        $mailerType = Model::getModuleSetting('Core', 'mailer_type', 'mail');

        if ($mailerType === 'smtp') {
            $this->transport = $this->getSmtpTransport();
        } else {
            $this->transport = $this->getMailTransport();
        }
    }

    /**
     * Create a new Fork Mailer Transport instance.
     *
     * @return Common\Mailer\Transport
     */
    public static function newInstance()
    {
        return new self();
    }

    public function isStarted()
    {
        return $this->transport->isStarted();
    }

    public function start()
    {
        return $this->transport->start();
    }

    public function stop()
    {
        return $this->transport->stop();
    }

    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        return $this->transport->send($message, $failedRecipients);
    }

    public function registerPlugin(\Swift_Events_EventListener $plugin)
    {
        return $this->transport->registerPlugin($plugin);
    }

    private function getSmtpTransport()
    {
        $server = Model::getModuleSetting('Core', 'smtp_server');
        $port = Model::getModuleSetting('Core', 'smtp_port', 25);
        $username = Model::getModuleSetting('Core', 'smtp_username');
        $password = Model::getModuleSetting('Core', 'smtp_password');

        return \Swift_SmtpTransport::newInstance($server, $port)
            ->setUsername($username)
            ->setPassword($password)
        ;
    }

    private function getMailTransport()
    {
        return \Swift_MailTransport::newInstance();
    }
}
