<?php

// require SpoonEmail
require_once 'spoon/email/email.php';


/**
 * BackendMailer
 *
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
	 * Adds an e-mail to the mailing queue, or sends it immediately if $queue is false.
	 *
	 * @param string $subject
	 * @param string $template
	 * @param array[optional] $variables
	 * @param mixed[optional] $to
	 * @param mixed[optional] $from
	 * @param mixed[optional] $replyTo
	 * @param bool[optional] $queue
	 */

	/**
	 * Adds an email to the queue.
	 *
	 * @return	void
	 * @param	string $subject
	 * @param	string $template
	 * @param	array[optional] $variables
	 * @param	string[optional] $toEmail
	 * @param	string[optional] $toName
	 * @param	string[optional] $fromEmail
	 * @param	string[optional] $fromName
	 * @param	bool[optional] $queue
	 */
	public static function addEmail($subject, $template, array $variables = null, $to = null, $from = null, $replyTo = null, $queue = true)
	{
		// SpoonEmail checks if to/from/reply-to are arrays or strings, but since this system saves the e-mails because of the queue
		// system, we have to run those steps in this bit as well. For this reason we also have a getTemplateContent() function here,
		// in spite of the SpoonTemplate::getContent() function, because we want to store the template with variables parsed in the database.

		// set defaults
		if($to === null) $to = BackendModel::getSetting('core', 'mailer_to');
		if($from === null) $from = BackendModel::getSetting('core', 'mailer_from');
		if($replyTo === null) $replyTo = BackendModel::getSetting('core', 'mailer_reply_to');

		// is the to given in an array format?
		if(is_array($to) && isset($to[0], $to[1]))
		{
			$email['to_email'] = (string) $to[0];
			$email['to_name'] = (string) $to[1];
		}

		// only an emailaddress is provided
		else
		{
			$email['to_email'] = (string) $to;
			$email['to_name'] = null;
		}

		// is the from given in an array format?
		if(is_array($from) && isset($from[0], $from[1]))
		{
			$email['from_email'] = (string) $from[0];
			$email['from_name'] = (string) $from[1];
		}

		// only an emailaddress is provided
		else
		{
			$email['from_email'] = (string) $from;
			$email['from_name'] = null;
		}

		// is the from given in an array format?
		if(is_array($replyTo) && isset($replyTo[0], $replyTo[1]))
		{
			$email['reply_to_email'] = (string) $replyTo[0];
			$email['reply_to_name'] = (string) $replyTo[1];
		}

		// only an emailaddress is provided
		else
		{
			$email['reply_to_email'] = (string) $replyTo;
			$email['reply_to_name'] = null;
		}

		// validate
		if(!SpoonFilter::isEmail($email['to_email'])) throw new BackendMailerException('Invalid e-mail address for recipient.');
		if(!SpoonFilter::isEmail($email['from_email'])) throw new BackendMailerException('Invalid e-mail address for sender.');

		// build array
		$email['subject'] = SpoonFilter::htmlentitiesDecode($subject);
		$email['html'] = self::getTemplateContent($template, $variables);
		if($queue) $email['send_on'] = date('Y-m-d H') .'00:00';

		// get db
		$db = BackendModel::getDB();

		// insert the email into the database
		$id = $db->insert('emails', $email);

		// if queue was not enabled, send this mail right away
		if(!$queue) self::send($id);
	}


	/**
	 * Returns the content from a given template
	 *
	 * @return	string
	 * @param	string	$template
	 * @param	array[optional]	$variables
	 */
	private static function getTemplateContent($template, $variables = null)
	{
		// new template instance
		$tpl = new SpoonTemplate();
		$tpl->setCompileDirectory(BACKEND_CACHE_PATH .'/templates');
		$tpl->setForceCompile(true);

		// variables were set
		if(!empty($variables)) $tpl->assign($variables);

		// return the content
		return (string) $tpl->getContent($template);
	}


	/**
	 * Send an email
	 *
	 * @return	void
	 * @param	int $id
	 */
	public static function send($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get record
		$emailRecord = (array) $db->getRecord('SELECT *
												FROM emails AS e
												WHERE e.id = ?;',
												array($id));

		// get settings
		$SMTPServer = BackendModel::getSetting('core', 'smtp_server');
		$SMTPPort = BackendModel::getSetting('core', 'smtp_port', 25);
		$SMTPUsername = BackendModel::getSetting('core', 'smtp_username');
		$SMTPPassword = BackendModel::getSetting('core', 'smtp_password');

		// create new SpoonEmail-instance
		$email = new SpoonEmail();
		$email->setTemplateCompileDirectory(BACKEND_CACHE_PATH .'/templates');

		// set authentication if needed
		if($SMTPUsername !== null && $SMTPPassword !== null)
		{
			// set server and connect with SMTP
			$email->setSMTPConnection($SMTPServer, $SMTPPort, 10);
			$email->setSMTPAuth($SMTPUsername, $SMTPPassword);
		}

		// set some properties
		$email->setFrom($emailRecord['from_email'], $emailRecord['from_name']);
		$email->addRecipient($emailRecord['to_email'], $emailRecord['to_name']);
		$email->setReplyTo($emailRecord['reply_to_email']);

		$email->setSubject($emailRecord['subject']);
		$email->setHTMLContent($emailRecord['html']);

		// send the email
		if($email->send())
		{
			// remove the email
			$db->delete('emails', 'id = ?', array($id));
		}
	}

}


/**
 * BackendMailer
 *
 * This class is used when an exceptions occures in the BackendMailer class
 *
 * @package		backend
 * @subpackage	mailer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendMailerException extends BackendException {}

?>