<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This test-email-action will test the mail-connection
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendSettingsAjaxTestEmailConnection extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// mailer type
		$mailerType = SpoonFilter::getPostValue('mailer_type', array('smtp', 'mail'), 'mail');

		// create new SpoonEmail-instance
		$email = new SpoonEmail();
		$email->setTemplateCompileDirectory(BACKEND_CACHE_PATH . '/compiled_templates');

		// send via SMTP
		if($mailerType == 'smtp')
		{
			// get settings
			$SMTPServer = SpoonFilter::getPostValue('smtp_server', null, '');
			$SMTPPort = SpoonFilter::getPostValue('smtp_port', null, '');
			$SMTPUsername = SpoonFilter::getPostValue('smtp_username', null, '');
			$SMTPPassword = SpoonFilter::getPostValue('smtp_password', null, '');

			if($SMTPServer == '') $this->output(self::BAD_REQUEST, null, BL::err('ServerIsRequired'));
			if($SMTPPort == '') $this->output(self::BAD_REQUEST, null, BL::err('PortIsRequired'));

			try
			{
				// set server and connect with SMTP
				$email->setSMTPConnection($SMTPServer, $SMTPPort, 10);
			}

			catch(SpoonEmailException $e)
			{
				$this->output(self::ERROR, null, $e->getMessage());
			}

			// set authentication if needed
			if($SMTPUsername != '' && $SMTPPassword != '') $email->setSMTPAuth($SMTPUsername, $SMTPPassword);
		}

		$fromEmail = SpoonFilter::getPostValue('mailer_from_email', null, '');
		$fromName = SpoonFilter::getPostValue('mailer_from_name', null, '');
		$toEmail = SpoonFilter::getPostValue('mailer_to_email', null, '');
		$toName = SpoonFilter::getPostValue('mailer_to_name', null, '');
		$replyToEmail = SpoonFilter::getPostValue('mailer_reply_to_email', null, '');
		$replyToName = SpoonFilter::getPostValue('mailer_reply_to_name', null, '');

		// validate
		if($fromEmail == '' || !SpoonFilter::isEmail($fromEmail)) $this->output(self::BAD_REQUEST, null, BL::err('EmailIsInvalid'));
		if($toEmail == '' || !SpoonFilter::isEmail($toEmail)) $this->output(self::BAD_REQUEST, null, BL::err('EmailIsInvalid'));
		if($replyToEmail == '' || !SpoonFilter::isEmail($replyToEmail)) $this->output(self::BAD_REQUEST, null, BL::err('EmailIsInvalid'));

		// set some properties
		$email->setFrom($fromEmail, $fromName);
		$email->addRecipient($toEmail, $toName);
		$email->setReplyTo($replyToEmail, $replyToName);
		$email->setSubject('Test');
		$email->setHTMLContent(BL::msg('TestMessage'));
		$email->setCharset(SPOON_CHARSET);

		try
		{
			if($email->send()) $this->output(self::OK, null, '');
			else $this->output(self::ERROR, null, 'unknown');
		}

		catch(SpoonEmailException $e)
		{
			$this->output(self::ERROR, null, $e->getMessage());
		}
	}
}
