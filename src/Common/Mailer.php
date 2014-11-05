<?php

namespace Common;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Frontend\Core\Engine\Model;
use Frontend\Core\Engine\Template;

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
     * @var \SpoonDatabase
     */
    private $database;

    /**
     * @param \SpoonDatabase $database
     */
    public function __construct($database)
    {
        $this->database = $database;
    }

    /**
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
        $subject = (string) strip_tags($subject);
        $template = (string) $template;

        // set defaults
        $to = Model::getModuleSetting('Core', 'mailer_to');
        $from = Model::getModuleSetting('Core', 'mailer_from');
        $replyTo = Model::getModuleSetting('Core', 'mailer_reply_to');

        // set recipient/sender headers
        $email['to_email'] = ($toEmail === null) ? (string) $to['email'] : $toEmail;
        $email['to_name'] = ($toName === null) ? (string) $to['name'] : $toName;
        $email['from_email'] = ($fromEmail === null) ? (string) $from['email'] : $fromEmail;
        $email['from_name'] = ($fromName === null) ? (string) $from['name'] : $fromName;
        $email['reply_to_email'] = ($replyToEmail === null) ? (string) $replyTo['email'] : $replyToEmail;
        $email['reply_to_name'] = ($replyToName === null) ? (string) $replyTo['name'] : $replyToName;

        // validate
        if (!\SpoonFilter::isEmail($email['to_email'])) {
            throw new \Exception('Invalid e-mail address for recipient.');
        }
        if (!\SpoonFilter::isEmail($email['from_email'])) {
            throw new \Exception('Invalid e-mail address for sender.');
        }
        if (!\SpoonFilter::isEmail($email['reply_to_email'])) {
            throw new \Exception('Invalid e-mail address for reply-to address.');
        }

        // build array
        $email['to_name'] = \SpoonFilter::htmlentitiesDecode($email['to_name']);
        $email['from_name'] = \SpoonFilter::htmlentitiesDecode($email['from_name']);
        $email['reply_to_name'] = \SpoonFilter::htmlentitiesDecode($email['reply_to_name']);
        $email['subject'] = \SpoonFilter::htmlentitiesDecode($subject);
        if ($isRawHTML) {
            $email['html'] = $template;
        } else {
            $email['html'] = $this->getTemplateContent($template, $variables);
        }
        if ($plainText !== null) {
            $email['plain_text'] = $plainText;
        }
        $email['created_on'] = Model::getUTCDate();

        // replace url's in the html content
        $email['html'] = $this->relativeToAbsolute($email['html']);
        if ($addUTM === true) {
            $email['html'] = $this->addUTM($email['html'], $subject);
        }

        // attachments added
        if (!empty($attachments)) {
            // add attachments one by one
            foreach ($attachments as $attachment) {
                // only add existing files
                if (is_file($attachment)) {
                    $email['attachments'][] = $attachment;
                }
            }

            // serialize :)
            if (!empty($email['attachments'])) {
                $email['attachments'] = serialize($email['attachments']);
            }
        }

        // set send date
        if ($queue) {
            if ($sendOn === null) {
                $email['send_on'] = Model::getUTCDate('Y-m-d H') . ':00:00';
            } else {
                $email['send_on'] = Model::getUTCDate('Y-m-d H:i:s', (int) $sendOn);
            }
        }

        // insert the email into the database
        $id = $this->database->insert('emails', $email);

        // trigger event
        Model::triggerEvent('Core', 'after_email_queued', array('id' => $id));

        // if queue was not enabled, send this mail right away
        if (!$queue) {
            $this->send($id);
        }

        // return
        return $id;
    }

    /**
     * @param string $html    The html to convert links in.
     * @param string $subject The subject of the mail
     * @return string
     */
    private function addUTM($html, $subject)
    {
        // match links
        $matches = array();
        preg_match_all('/href="(http:\/\/(.*))"/iU', $html, $matches);

        // any links?
        $utm = array('utm_source' => 'mail', 'utm_medium' => 'email', 'utm_campaign' => Uri::getUrl($subject));
        if (isset($matches[0]) && !empty($matches[0])) {
            $searchLinks = array();
            $replaceLinks = array();

            // loop old links
            foreach ($matches[1] as $i => $link) {
                $searchLinks[] = $matches[0][$i];
                $replaceLinks[] = 'href="' . Model::addURLParameters($link, $utm) . '"';
            }

            $html = str_replace($searchLinks, $replaceLinks, $html);
        }

        return $html;
    }

    /**
     * Get all queued mail ids
     *
     * @return array
     */
    public function getQueuedMailIds()
    {
        return (array) $this->database->getColumn(
            'SELECT e.id
             FROM emails AS e
             WHERE e.send_on < ? OR e.send_on IS NULL',
            array(Model::getUTCDate())
        );
    }

    /**
     * Returns the content from a given template
     *
     * @param string $template  The template to use.
     * @param array  $variables The variables to assign.
     * @return string
     */
    private function getTemplateContent($template, $variables = null)
    {
        // new template instance
        $tpl = new Template(false);

        // set some options
        $tpl->setForceCompile(true);

        // variables were set
        if (!empty($variables)) {
            $tpl->assign($variables);
        }

        // grab the content
        $content = $tpl->getContent($template);

        // replace internal links/images
        $search = array('href="/', 'src="/');
        $replace = array('href="' . SITE_URL . '/', 'src="' . SITE_URL . '/');
        $content = str_replace($search, $replace, $content);

        // create instance
        $cssToInlineStyles = new CSSToInlineStyles();

        // set some properties
        $cssToInlineStyles->setHTML($content);
        $cssToInlineStyles->setUseInlineStylesBlock(true);
        $cssToInlineStyles->setEncoding(SPOON_CHARSET);

        // return the content
        return (string) $cssToInlineStyles->convert();
    }

    /**
     * @param string $html  The html to convert links in.
     * @return string
     */
    private function relativeToAbsolute($html)
    {
        // get internal links
        $matches = array();
        preg_match_all('|href="/(.*)"|i', $html, $matches);

        // any links?
        if (!empty($matches[0])) {
            $search = array();
            $replace = array();

            // loop the links
            foreach ($matches[0] as $key => $link) {
                $search[] = $link;
                $replace[] = 'href="' . SITE_URL . '/' . $matches[1][$key] . '"';
            }

            $html = str_replace($search, $replace, $html);
        }

        return $html;
    }

    /**
     * Send an email
     *
     * @param int $id The id of the mail to send.
     */
    public function send($id)
    {
        $id = (int) $id;

        // get record
        $emailRecord = (array) $this->database->getRecord(
            'SELECT *
             FROM emails AS e
             WHERE e.id = ?',
            array($id)
        );

        // mailer type
        $mailerType = Model::getModuleSetting('Core', 'mailer_type', 'mail');

        // create new \SpoonEmail-instance
        $email = new \SpoonEmail();
        $email->setTemplateCompileDirectory(FRONTEND_CACHE_PATH . '/CompiledTemplates');

        // send via SMTP
        if ($mailerType == 'smtp') {
            // get settings
            $SMTPServer = Model::getModuleSetting('Core', 'smtp_server');
            $SMTPPort = Model::getModuleSetting('Core', 'smtp_port', 25);
            $SMTPUsername = Model::getModuleSetting('Core', 'smtp_username');
            $SMTPPassword = Model::getModuleSetting('Core', 'smtp_password');

            // set security if needed
            $secureLayer = Model::getModuleSetting('Core', 'smtp_secure_layer');
            if (in_array($secureLayer, array('ssl', 'tls'))) {
                $email->setSMTPSecurity($secureLayer);
            }

            // set server and connect with SMTP
            $email->setSMTPConnection($SMTPServer, $SMTPPort, 10);

            // set authentication if needed
            if ($SMTPUsername !== null && $SMTPPassword !== null) {
                $email->setSMTPAuth($SMTPUsername, $SMTPPassword);
            }
        }

        // set some properties
        $email->setFrom($emailRecord['from_email'], $emailRecord['from_name']);
        $email->addRecipient($emailRecord['to_email'], $emailRecord['to_name']);
        $email->setReplyTo($emailRecord['reply_to_email']);
        $email->setSubject($emailRecord['subject']);
        $email->setHTMLContent($emailRecord['html']);
        $email->setCharset(SPOON_CHARSET);
        $email->setContentTransferEncoding('base64');
        if ($emailRecord['plain_text'] != '') {
            $email->setPlainContent($emailRecord['plain_text']);
        }

        // attachments added
        if (isset($emailRecord['attachments']) && $emailRecord['attachments'] !== null) {
            // unserialize
            $attachments = (array) unserialize($emailRecord['attachments']);

            // add attachments to email
            foreach ($attachments as $attachment) {
                $email->addAttachment($attachment);
            }
        }

        // send the email
        if ($email->send()) {
            // remove the email
            $this->database->delete('emails', 'id = ?', array($id));

            // trigger event
            Model::triggerEvent('Core', 'after_email_sent', array('id' => $id));
        }
    }
}
