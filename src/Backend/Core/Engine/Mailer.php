<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

use Backend\Core\Engine\Model as BackendModel;

/**
 * This class will send mails
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Sam Tubbax <sam@sumocoders.be>
 */
class Mailer extends \Common\Mailer
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
     * @param string $replyToEmail The replyto-address for the mail.
     * @param string $replyToName  The replyto-name for the mail.
     * @param bool   $queue        Should the mail be queued?
     * @param int    $sendOn       When should the email be send, only used when $queue is true.
     * @param bool   $isRawHTML    If this is true $template will be handled as raw HTML, so no parsing of
     *                             $variables is done.
     * @param string $plainText    The plain text version.
     * @param array  $attachments  Paths to attachments to include.
     * @param bool   $addUTM       Add UTM tracking to the urls.
     * @return int The id of the inserted mail.
     */
    public static function addEmail(
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
        parent::addEmail(
            $subject,
            $template,
            $variables,
            $toEmail,
            $toName,
            $fromEmail,
            $fromName,
            $replyToEmail,
            $replyToName,
            $queue,
            $sendOn,
            $isRawHTML,
            $plainText,
            $attachments,
            $addUTM
        );
    }
}
