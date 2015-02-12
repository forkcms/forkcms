<?php

namespace Common;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Frontend\Core\Engine\Model;

/**
 * This class will send mails
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Sam Tubbax <sam@sumocoders.be>
 * @author Wouter Sioen <wouter@wijs.be>
 */
class Mailer
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @deprecated build a message object and send it trough the send method
     *
     * Adds an email to the queue.
     *
     * @param string $subject      The subject for the email.
     * @param string $template     The template to use.
     * @param array  $variables    Variables that should be assigned in the email.
     * @param string $toEmail      The to-address for the email.
     * @param string $toName       The to-name for the email.
     * @param string $fromEmail    The from-address for the mail.
     * @param string $fromName     The from-name for the mail.
     * @param string $replyToEmail The reply to-address for the mail.
     * @param string $replyToName  The reply to-name for the mail.
     * @param bool   $queue        Should the mail be queued?
     * @param int    $sendOn       When should the email be send, only used when $queue is true.
     * @param bool   $isRawHTML    If this is true $template will be handled as raw HTML, so no parsing of
     *                             $variables is done.
     * @param string $plainText    The plain text version.
     * @param array  $attachments  Paths to attachments to include.
     * @param bool   $addUTM       Add UTM tracking to the urls.
     * @return int
     */
    public function addEmail(
        $subject,
        $template,
        array $variables = null,
        $toEmail = null,
        $toName = null,
        $fromEmail = null,
        $fromName = null,
        $replyToEmail = null,
        $replyToName = null,
        $queue = false,
        $sendOn = null,
        $isRawHTML = false,
        $plainText = null,
        array $attachments = null,
        $addUTM = false
    ) {
        // set recipient/sender headers
        $to = Model::getModuleSetting('Core', 'mailer_to');
        $from = Model::getModuleSetting('Core', 'mailer_from');
        $replyTo = Model::getModuleSetting('Core', 'mailer_reply_to');
        $toEmail = ($toEmail === null) ? (string) $to['email'] : $toEmail;
        $toName = ($toName === null) ? (string) $to['name'] : $toName;
        $fromEmail = ($fromEmail === null) ? (string) $from['email'] : $fromEmail;
        $fromName = ($fromName === null) ? (string) $from['name'] : $fromName;
        $replyToEmail = ($replyToEmail === null) ? (string) $replyTo['email'] : $replyToEmail;
        $replyToName = ($replyToName === null) ? (string) $replyTo['name'] : $replyToName;

        $message = \Common\Mailer\Message::newInstance($subject)
            ->setFrom(array($fromEmail => $fromName))
            ->setTo(array($toEmail => $toName))
            ->setReplyTo(array($replyToEmail => $replyToName))
            ->parseHtml($template, $variables, $addUTM)
            ->setPlainText($plainText)
            ->addAttachments($attachments);
        ;

        // attachments added
        $this->send($message);

        // trigger event
        Model::triggerEvent('Core', 'after_email_sent', array('message' => $message));
    }

    /**
     * send a Swift_Message like message
     *
     * @param  Swift_mime_message $message
     * @return boolean
     */
    public function send(\Swift_Mime_Message $message)
    {
        $transport = \Common\Mailer\TransportFactory::create(
            Model::getModuleSetting('Core', 'mailer_type', 'mail'),
            Model::getModuleSetting('Core', 'smtp_server'),
            Model::getModuleSetting('Core', 'smtp_port', 25),
            Model::getModuleSetting('Core', 'smtp_username'),
            Model::getModuleSetting('Core', 'smtp_password')
        );
        $mailer = \Swift_Mailer::newInstance($transport);

        $this->logger->info('Sending email: ' . $message->getSubject());

        return $mailer->send($message);
    }
}
