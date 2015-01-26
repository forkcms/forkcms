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
use Backend\Core\Engine\Template as BackendTemplate;

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

        // build html
        $html = '';
        if ($isRawHTML) {
            $html = $template;
        } else {
            $html = $this->getTemplateContent($template, $variables);
        }
        $html = $this->relativeToAbsolute($html);
        if ($addUTM === true) {
            $html = $this->addUTM($html, $subject);
        }

        $transport = \Common\Mailer\Transport::newInstance();

        $message = \Swift_message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($fromEmail => $fromName))
            ->setTo(array($toEmail => $toName))
            ->setReplyTo(array($replyToEmail => $replyToName))
            ->setBody($html, 'text/html')
        ;

        if ($plainText !== null) {
            $message->addPart($plainText, 'text/plain');
        }

        // attachments added
        if (!empty($attachments)) {
            // add attachments one by one
            foreach ($attachments as $attachment) {
                // only add existing files
                if (is_file($attachment)) {
                    $message->attach(\Swift_Attachment::fromPath($attachment));
                }
            }
        }

        $mailer = \Swift_Mailer::newInstance($transport);
        $mailer->send($message);

        // trigger event
        Model::triggerEvent('Core', 'after_email_sent', array('message' => $message));
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
     * Returns the content from a given template
     *
     * @param string $template  The template to use.
     * @param array  $variables The variables to assign.
     * @return string
     */
    private function getTemplateContent($template, $variables = null)
    {
        // new template instance
        $tpl = null;
        if (APPLICATION === 'Backend') {
            $tpl = new BackendTemplate(false);
        } else {
            $tpl = new Template(false);
        }

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
}
