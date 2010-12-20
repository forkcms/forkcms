<?php

/**
 * This class will send mails
 *
 * @package		backend
 * @subpackage	mailer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailer
{
	/**
	 * Adds an email to the queue.
	 *
	 * @return	void
	 * @param	string $subject					The subject for the email.
	 * @param	string $template				The template to use.
	 * @param	array[optional] $variables		Variables that should be assigned in the email.
	 * @param	string[optional] $toEmail		The to-address for the email.
	 * @param	string[optional] $toName		The to-name for the email.
	 * @param	string[optional] $fromEmail		The from-address for the mail.
	 * @param	string[optional] $fromName		The from-name for the mail.
	 * @param	bool[optional] $queue			Should the mail be queued?
	 * @param	int[optional] $sendOn			When should the email be send, only used when $queue is true.
	 * @param	bool[optional] $isRawHTML		If this is true $template will be handled as raw HTML, so no parsing of $variables is done.
	 * @param	string[optional] $plaintText	The plain text version.
	 */
	public static function addEmail($subject, $template, array $variables = null, $toEmail = null, $toName = null, $fromEmail = null, $fromName = null, $replyToEmail = null, $replyToName = null, $queue = false, $sendOn = null, $isRawHTML = false, $plainText = null)
	{
		// redefine
		$subject = (string) $subject;
		$template = (string) $template;

		// set defaults
		$to = BackendModel::getModuleSetting('core', 'mailer_to');
		$from = BackendModel::getModuleSetting('core', 'mailer_from');
		$replyTo = BackendModel::getModuleSetting('core', 'mailer_reply_to');

		// set recipient/sender headers
		$email['to_email'] = (empty($toEmail)) ? (string) $to['email'] : $toEmail;
		$email['to_name'] = (empty($toName)) ? (string) $to['name'] : $toName;
		$email['from_email'] = (empty($fromEmail)) ? (string) $from['email'] : $fromEmail;
		$email['from_name'] = (empty($fromName)) ? (string) $from['name'] : $fromName;
		$email['reply_to_email'] = (empty($replyToEmail)) ? (string) $replyTo['email'] : $replyToEmail;
		$email['reply_to_name'] = (empty($replyToName)) ? (string) $replyTo['name'] : $replyToName;

		// validate
		if(!empty($email['to_email']) && !SpoonFilter::isEmail($email['to_email'])) throw new BackendException('Invalid e-mail address for recipient.');
		if(!empty($email['from_email']) && !SpoonFilter::isEmail($email['from_email'])) throw new BackendException('Invalid e-mail address for sender.');
		if(!empty($email['reply_to_email']) && !SpoonFilter::isEmail($email['reply_to_email'])) throw new BackendException('Invalid e-mail address for reply-to address.');

		// build array
		$email['subject'] = SpoonFilter::htmlentitiesDecode($subject);
		if($isRawHTML) $email['html'] = $template;
		else $email['html'] = self::getTemplateContent($template, $variables);
		if($plainText !== null) $email['plain_text'] = $plainText;
		$email['created_on'] = BackendModel::getUTCDate();

		// set send date
		if($queue)
		{
			if($sendOn === null) $email['send_on'] = BackendModel::getUTCDate('Y-m-d H') .':00:00';
			else $email['send_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', (int) $sendOn);
		}

		// insert the email into the database
		$id = BackendModel::getDB(true)->insert('emails', $email);

		// if queue was not enabled, send this mail right away
		if(!$queue) self::send($id);
	}


	/**
	 * Returns the content from a given template
	 *
	 * @return	string
	 * @param	string	$template				The template to use.
	 * @param	array[optional]	$variables		The variabled to assign.
	 */
	private static function getTemplateContent($template, $variables = null)
	{
		// new template instance
		$tpl = new BackendTemplate(false);

		// set some options
		$tpl->setForceCompile(true);

		// variables were set
		if(!empty($variables)) $tpl->assign($variables);

		// grab the content
		$content = $tpl->getContent($template);

		// replace internal links/images
		$search = array('href="/', 'src="/');
		$replace = array('href="'. SITE_URL .'/', 'src="'. SITE_URL .'/');
		$content = str_replace($search, $replace, $content);

		// require CSSToInlineStyles
		require_once 'external/css_to_inline_styles.php';

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
	 * Get all queued mail ids
	 *
	 * @return	array
	 */
	public static function getQueuedMailIds()
	{
		// return the ids
		return (array) BackendModel::getDB()->getColumn('SELECT e.id
															FROM emails AS e
															WHERE e.send_on < ?',
															array(BackendModel::getUTCDate()));
	}


	/**
	 * Send an email
	 *
	 * @return	void
	 * @param	int $id		The id of the mail to send.
	 */
	public static function send($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// get record
		$emailRecord = (array) $db->getRecord('SELECT *
												FROM emails AS e
												WHERE e.id = ?',
												array($id));

		// mailer type
		$mailerType = BackendModel::getModuleSetting('core', 'mailer_type', 'mail');

		// create new SpoonEmail-instance
		$email = new SpoonEmail();
		$email->setTemplateCompileDirectory(BACKEND_CACHE_PATH .'/compiled_templates');

		// send via SMTP
		if($mailerType == 'smtp')
		{
			// get settings
			$SMTPServer = BackendModel::getModuleSetting('core', 'smtp_server');
			$SMTPPort = BackendModel::getModuleSetting('core', 'smtp_port', 25);
			$SMTPUsername = BackendModel::getModuleSetting('core', 'smtp_username');
			$SMTPPassword = BackendModel::getModuleSetting('core', 'smtp_password');

			// set authentication if needed
			if($SMTPUsername !== null && $SMTPPassword !== null)
			{
				// set server and connect with SMTP
				$email->setSMTPConnection($SMTPServer, $SMTPPort, 10);
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
		if($emailRecord['plain_text'] != '') $email->setPlainContent($emailRecord['plain_text']);

		// send the email
		if($email->send())
		{
			// remove the email
			$db->delete('emails', 'id = ?', array($id));
		}
	}
}

?>