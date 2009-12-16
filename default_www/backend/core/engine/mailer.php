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
 * @since		2.0
 */
class BackendMailer
{
	/**
	 * @todo	doc
	 * @param unknown_type $html
	 */
	private static function convertHTMLToPlainText($html)
	{
		return $html;
	}


	/**
	 * @todo	doc
	 *
	 * @param unknown_type $subject
	 * @param unknown_type $templateFile
	 * @param array $variables
	 * @param unknown_type $to
	 * @param unknown_type $from
	 */
	public static function addEmail($subject, $templateFile, array $variables, $to = null, $from = null, $queue = true)
	{
		// redefine
		if($to === null) $to = array('no-reply@fork-cms.be', 'Fork CMS'); // @todo	Get from settings
		if($from === null) $from = array('no-reply@fork-cms.be', 'Fork CMS'); // @todo	Get from settings
		$replyToEmail = 'no-reply@fork-cms.be';


		// is the to given in an array format?
		if(is_array($to) && isset($to[0], $to[1]))
		{
			$toEmail = (string) $to[0];
			$toName = (string) $to[1];
		}

		// only an emailaddress is provided
		else
		{
			$toEmail = (string) $to;
			$toName = null;
		}

		// is the from given in an array format?
		if(is_array($from) && isset($from[0], $from[1]))
		{
			$fromEmail = (string) $from[0];
			$fromName = (string) $from[1];
		}

		// only an emailaddress is provided
		else
		{
			$fromEmail = (string) $from;
			$fromName = null;
		}

		// validate
		if(!SpoonFilter::isEmail($toEmail)) throw new BackendMailerException('Invalid emailaddress for to.');
		if(!SpoonFilter::isEmail($fromEmail)) throw new BackendMailerException('Invalid emailaddress for to.');

		// build array
		$email['to_email'] = $toEmail;
		if($toName !== null) $email['to_name'] = $toName;
		$email['from_email'] = $fromEmail;
		if($fromName !== null)$email['from_name'] = $fromName;
		$email['reply_to_email'] = $replyToEmail;
		$email['subject'] = SpoonFilter::htmlentitiesDecode($subject);
		$email['HTML'] = self::getTemplateContent($templateFile, $variables);
		$email['plain_text'] = self::convertHTMLToPlainText($email['HTML']);
		if($queue) $email['date_to_send'] = date('Y-m-d H') .'00:00';

		// get db
		$db = BackendModel::getDB();

		$id = $db->insert('emails', $email);

		if(!$queue) self::send($id);
	}


	/**
	 * @todo	doc
	 *
	 * @param unknown_type $template
	 * @param array $variabeles
	 */
	private static function getTemplateContent($template, array $variabeles = null)
	{
		// declare template
		$tpl = new SpoonTemplate();
		$tpl->setCompileDirectory(BACKEND_CACHE_PATH .'/templates');
		$tpl->setForceCompile(true);

		// parse variables in the template if any are found
		if(!empty($variables)) $tpl->assign($variables);

		// turn on output buffering
		ob_start();

		// html body
		$tpl->display($template);

		// return template content
		return ob_get_clean();
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
		$SMTPServer = BackendModel::getModuleSetting('core', 'smtp_server');
		$SMTPPort = BackendModel::getModuleSetting('core', 'smtp_port', 25);
		$SMTPUsername = BackendModel::getModuleSetting('core', 'smtp_username');
		$SMTPPassword = BackendModel::getModuleSetting('core', 'smtp_password');

		// create new SpoonEmail-instance
		$email = new SpoonEmail();
		$email->setTemplateCompileDirectory(BACKEND_CACHE_PATH .'/templates');

		// set server
		$email->setSMTPConnection($SMTPServer, $SMTPPort, 10);

		// set authentication if needed
		if($SMTPUsername !== null && $SMTPPassword !== null) $email->setSMTPAuth($SMTPUsername, $SMTPPassword);

		// set some properties
		$email->setFrom($emailRecord['from_email'], $emailRecord['from_name']);
		$email->addRecipient($emailRecord['to_email'], $emailRecord['to_name']);
		$email->setReplyTo($emailRecord['reply_to_email']);

		$email->setSubject($emailRecord['subject']);
		$email->setHTMLContent($emailRecord['HTML']);
		$email->setPlainContent($emailRecord['plain_text']);

		// send the email
		if($email->send())
		{
			// remove the email
			$db->delete('emails', 'id = ?', array($id));
		}
	}


}

class BackendMailerException extends BackendException
{

}


?>