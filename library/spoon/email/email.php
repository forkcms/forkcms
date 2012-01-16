<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	email
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */


/**
 * This class is used to send emails
 *
 * @package		spoon
 * @subpackage	email
 *
 *
 * @author		Dave Lens <dave@spoon-library.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		1.0.0
 */
class SpoonEmail
{
	/**
	 * Line feed
	 *
	 * @var	string
	 */
	const LF = "\n";


	/**
	 * Attachments storage
	 *
	 * @var	array
	 */
	private $attachments = array();


	/**
	 * BCC storage
	 *
	 * @var	array
	 */
	private $BCC = array();


	/**
	 * CC storage
	 *
	 * @var	array
	 */
	private $CC = array();


	/**
	 * Charset
	 *
	 * @var string
	 */
	private $charset = 'utf-8';


	/**
	 * Template compile directory
	 *
	 * @var string
	 */
	private $compileDirectory;


	/**
	 * Email content storage
	 *
	 * @var array
	 */
	private $content = array('html' => '', 'plain' => '');


	/**
	 * Content transfer encoding value for the html and plaintext content.
	 *
	 * @var	string
	 */
	private $contentTransferEncoding = '8bit';


	/**
	 * Content type. Multipart/alternative by default
	 *
	 * @var	string
	 */
	private $contentType = 'multipart/alternative';


	/**
	 * Debug status
	 *
	 * @var	bool
	 */
	private $debug = false;


	/**
	 * Sender information
	 *
	 * @var	array
	 */
	private $from = array('name' => '', 'email' => '');


	/**
	 * Headers string
	 *
	 * @var string
	 */
	private $headers = '';


	/**
	 * Mailing method. Can be 'mail' or 'smtp'
	 *
	 * @var	string
	 */
	private $method = 'mail';


	/**
	 * E-mail priority storage (1 = high, 3 = normal, 5 = low)
	 *
	 * @var	int
	 */
	private $priority = 3;


	/**
	 * Regular recipients storage
	 *
	 * @var	array
	 */
	private $recipients = array();


	/**
	 * Reply-To storage
	 *
	 * @var array
	 */
	private $replyTo;


	/**
	 * Security layer
	 *
	 * @var string
	 */
	private $security;


	/**
	 * SMTP object instance
	 *
	 * @var	SpoonEmailSMTP
	 */
	private $SMTP;


	/**
	 * E-mail subject storage
	 *
	 * @var	string
	 */
	private $subject;


	/**
	 * Initial To: storage
	 *
	 * @var	array
	 */
	private $to = array('name' => '', 'email' => '');


	/**
	 * Adds an attachment to the headers.
	 *
	 * @param	string $filename				The path to (including the filename for) the attachment.
	 * @param	string[optional] $newName		The new name of the attachment.
	 * @param	string[optional] $disposition	The disposition of the attachment. Can be 'attachment' or 'inline'.
	 * @param	string[optional] $encoding		The attachment encoding (only base64 for now).
	 */
	public function addAttachment($filename, $newName = null, $disposition = 'attachment', $encoding = 'base64')
	{
		// check input
		if(!SpoonFile::exists($filename)) throw new SpoonEmailException('File not found.');

		// no name was found in the input
		if(empty($newName))
		{
			// use the source file's base name
			$newName = basename($filename);
		}

		// store file extension
		$extension = SpoonFile::getExtension($newName);

		// store attachment disposition
		$disposition = SpoonFilter::getValue($disposition, array('attachment', 'inline'), 'attachment');

		// store type according to disposition
		if($disposition === 'attachment') $extension = 'default';

		// store file info
		$this->attachments[] = array(
			'file' => $filename,
			'name' => $newName,
			'encoding' => $encoding,
			'type' => $this->getAttachmentContentType($extension),
			'disposition' => $disposition,
			'data' => chunk_split(base64_encode(SpoonFile::getContent($filename)))
		);
	}


	/**
	 * Adds a blind carbon copy recipient to the BCC stack.
	 *
	 * @param	string $email			The BCC e-mail address.
	 * @param	string[optional] $name	The BCC name.
	 */
	public function addBCC($email, $name = null)
	{
		// check input
		if(!SpoonFilter::isEmail($email)) throw new SpoonEmailException('No valid e-mail address given.');

		// add CC email and name to stack
		$this->BCC[] = array('name' => (string) $name, 'email' => (string) $email);
	}


	/**
	 * Adds a carbon copy recipient to the CC stack.
	 *
	 * @param	string $email				The CC e-mail address.
	 * @param	string[optional] $name		The CC name.
	 */
	public function addCC($email, $name = null)
	{
		// check input
		if(!SpoonFilter::isEmail($email)) throw new SpoonEmailException('No valid e-mail address given.');

		// add CC email and name to stack
		$this->CC[] = array('name' => (string) $name, 'email' => (string) $email);
	}


	/**
	 * Adds a single-line header to the email headers.
	 *
	 * @param	string $header		The full content for the header.
	 */
	public function addHeader($header)
	{
		$this->headers .= (string) $header . self::LF;
	}


	/**
	 * Adds a regular recipient to the recipients stack.
	 *
	 * @param	string $email				The recipient e-mail address.
	 * @param	string[optional] $name		The name of the recipient.
	 */
	public function addRecipient($email, $name = null)
	{
		// check input
		if(!SpoonFilter::isEmail($email)) throw new SpoonEmailException('No valid e-mail address given.');

		// add recipient email and name to stack
		$this->recipients[] = array('name' => (string) $name, 'email' => (string) $email);
	}


	/**
	 * Adds an array of recipients to the recipients stack.
	 *
	 * @param	array $recipients		A multidimensional array with recipients. Will read the first 2 items in each subsequent array.
	 */
	public function addRecipientArray(array $recipients)
	{
		// loop recipients
		foreach($recipients as $recipient)
		{
			// we need the values, not the keys
			$recipient = array_values($recipient);

			// store recipient parameters
			(SpoonFilter::isEmail($recipient[0])) ? $email = $recipient[0] : $name = $recipient[1];
			(SpoonFilter::isEmail($recipient[1])) ? $email = $recipient[1] : $name = $recipient[0];

			// check if there's an email found, if so we store it
			if(SpoonFilter::isEmail($email)) $this->addRecipient($email, $name);
		}
	}


	/**
	 * Closes the current SMTP connection.
	 */
	public function closeConnection()
	{
		// no smtp instance found
		if($this->SMTP === null) throw new SpoonEmailException('You can\'t close what isn\'t open.');

		// close connection
		$this->SMTP->quit();
	}


	/**
	 * Apply the content tranfer encoding.
	 *
	 * @return	string
	 * @param	string $content		Content to apply the encoding to.
	 */
	private function encodeContent($content)
	{
		// apply specific encoding
		switch($this->contentTransferEncoding)
		{
			case 'base64':
				$content = chunk_split(base64_encode($content));
			break;
		}

		return $content;
	}


	/**
	 * Gets attachment content MIME type for given file extension.
	 *
	 * @return	string
	 * @param	string $extension	The extension to look up.
	 */
	private function getAttachmentContentType($extension)
	{
		// content types listed by extension
		$types = array(	'default' => 'application/octet-stream',
			'ai' => 'application/postscript',
			'aif' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'avi' => 'video/x-msvideo',
			'bin' => 'application/macbinary',
			'bmp' => 'image/bmp',
			'cpt' => 'application/mac-compactpro',
			'css' => 'text/css',
			'dcr' => 'application/x-director',
			'dir' => 'application/x-director',
			'doc' => 'application/msword',
			'dvi' => 'application/x-dvi',
			'dxr' => 'application/x-director',
			'eml' => 'message/rfc822',
			'eps' => 'application/postscript',
			'gif' => 'image/gif',
			'gtar' => 'application/x-gtar',
			'hqx' => 'application/mac-binhex40',
			'htm' => 'text/html',
			'html' => 'text/html',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'js' => 'application/x-javascript',
			'log' => 'text/plain',
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'mif' => 'application/vnd.mif',
			'mov' => 'video/quicktime',
			'movie' => 'video/x-sgi-movie',
			'mp2' => 'audio/mpeg',
			'mp3' => 'audio/mpeg',
			'mpe' => 'video/mpeg',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpga' => 'audio/mpeg',
			'oda' => 'application/oda',
			'pdf' => 'application/pdf',
			'php' => 'application/x-httpd-php',
			'php3' => 'application/x-httpd-php',
			'php4' => 'application/x-httpd-php',
			'phps' => 'application/x-httpd-php-source',
			'phtml' => 'application/x-httpd-php',
			'png' => 'image/png',
			'ppt' => 'application/vnd.ms-powerpoint',
			'ps' => 'application/postscript',
			'qt' => 'video/quicktime',
			'ra' => 'audio/x-realaudio',
			'ram' => 'audio/x-pn-realaudio',
			'rm' => 'audio/x-pn-realaudio',
			'rpm' => 'audio/x-pn-realaudio-plugin',
			'rtf' => 'text/rtf',
			'rtx' => 'text/richtext',
			'rv' => 'video/vnd.rn-realvideo',
			'shtml' => 'text/html',
			'sit' => 'application/x-stuffit',
			'smi' => 'application/smil',
			'smil' => 'application/smil',
			'swf' => 'application/x-shockwave-flash',
			'tar' => 'application/x-tar',
			'text' => 'text/plain',
			'tgz' => 'application/x-tar',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'txt' => 'text/plain',
			'wav' => 'audio/x-wav',
			'wbxml' => 'application/vnd.wap.wbxml',
			'wmlc' => 'application/vnd.wap.wmlc',
			'word' => 'application/msword',
			'xht' => 'application/xhtml+xml',
			'xhtml' => 'application/xhtml+xml',
			'xl' => 'application/excel',
			'xls' => 'application/vnd.ms-excel',
			'xml' => 'text/xml',
			'xsl' => 'text/xml',
			'zip' => 'application/zip'
		);

		// return default if no (or unknown) extension is provided
		return ($extension === null || empty($types[$extension])) ? $types['default'] : $types[$extension];
	}


	/**
	 * Returns the SpoonEmail debugging status.
	 *
	 * @return	bool
	 */
	public function getDebug()
	{
		return $this->debug;
	}


	/**
	 * Builds the e-mail headers.
	 */
	private function getHeaders()
	{
		// create boundaries
		$uniqueId = md5(uniqid(time()));
		$boundary = 'SEB1_' . $uniqueId;
		$secondBoundary = 'SEB2_' . $uniqueId;

		// if plain body is not set, we'll strip the HTML tags from the HTML body
		if(empty($this->content['plain'])) $this->content['plain'] = SpoonFilter::stripHTML($this->content['html'], null, true);

		// encode the content
		$this->content['html'] = $this->encodeContent($this->content['html']);
		$this->content['plain'] = $this->encodeContent($this->content['plain']);

		// build headers
		$this->addHeader('Date: ' . SpoonDate::getDate('r'));
		$this->addHeader('From: ' . $this->from['name'] . ' <' . $this->from['email'] . '>');

		// check mailmethod, some media don't need these (like mail())
		if($this->method == 'smtp')
		{
			// set subject
			$this->addHeader('Subject: ' . $this->subject);

			// set general To: header. useful if you prefer to customize it
			if(!empty($this->to['name'])) $this->addHeader('To: ' . $this->to['name'] . ' <' . $this->to['email'] . '>');

			// no To: set so we add recipients to the headers
			else $this->addHeader('To: ' . $this->reformatRecipientString($this->recipients));
		}

		// loop and add CCs to headers
		if(!empty($this->CC)) $this->addHeader('cc: ' . $this->reformatRecipientString($this->CC));

		// loop and add BCCs to headers
		if(!empty($this->BCC)) $this->addHeader('bcc: ' . $this->reformatRecipientString($this->BCC));

		// add Reply-To header to headers
		if(!empty($this->replyTo)) $this->addHeader('Reply-To: ' . $this->reformatRecipientString($this->replyTo));

		// if attachments are set, change the mail content type
		if(!empty($this->attachments)) $this->contentType = 'multipart/mixed';

		// continue the rest of the headers
		$this->addHeader('X-Priority: ' . $this->priority);
		$this->addHeader('X-Mailer: SpoonEmail (part of Spoon library - http://www.spoon-library.com)');
		$this->addHeader('MIME-Version: 1.0');
		$this->addHeader('Content-Type: ' . $this->contentType . '; charset="' . $this->charset . '"; boundary="' . $boundary . '"' . self::LF);
		$this->addHeader('Importance: normal');
		$this->addHeader('Priority: normal');
		$this->addHeader('This is a multi-part message in MIME format.' . self::LF);
		$this->addHeader('--' . $boundary);

		// attachments found
		if(!empty($this->attachments))
		{
			// means we need a second boundary defined to send html/plain mails.
			$this->addHeader('Content-Type: multipart/alternative; boundary="' . $secondBoundary . '"' . self::LF);
			$this->addHeader('--' . $secondBoundary);
			$this->addHeader('Content-Type: text/plain; charset="' . $this->charset . '"');
			$this->addHeader('Content-Disposition: inline');
			$this->addHeader('Content-Transfer-Encoding: ' . $this->contentTransferEncoding . self::LF);
			$this->addHeader($this->content['plain'] . self::LF);
			$this->addHeader('--' . $secondBoundary);
			$this->addHeader('Content-Type: text/html; charset="' . $this->charset . '"');
			$this->addHeader('Content-Disposition: inline');
			$this->addHeader('Content-Transfer-Encoding: ' . $this->contentTransferEncoding . self::LF);
			$this->addHeader($this->content['html'] . self::LF);
			$this->addHeader('--' . $secondBoundary . '--');
		}

		// no attachments
		else
		{
			// continue the rest of the headers
			$this->addHeader('Content-Type: text/plain; charset="' . $this->charset . '"');
			$this->addHeader('Content-Disposition: inline');
			$this->addHeader('Content-Transfer-Encoding: ' . $this->contentTransferEncoding . self::LF);
			$this->addHeader($this->content['plain'] . self::LF);
			$this->addHeader('--' . $boundary);
			$this->addHeader('Content-Type: text/html; charset="' . $this->charset . '"');
			$this->addHeader('Content-Disposition: inline');
			$this->addHeader('Content-Transfer-Encoding: ' . $this->contentTransferEncoding . self::LF);
			$this->addHeader($this->content['html'] . self::LF);
		}

		// attachments found
		if(!empty($this->attachments))
		{
			// loop attachments
			foreach($this->attachments as $attachment)
			{
				// set attachment headers
				$this->addHeader('--' . $boundary);
				$this->addHeader('Content-Type: ' . $attachment['type'] . '; name="' . $attachment['name'] . '"');
				$this->addHeader('Content-Transfer-Encoding: ' . $attachment['encoding']);
				$this->addHeader('Content-Disposition: ' . $attachment['disposition'] . '; filename="' . $attachment['name'] . '"' . self::LF);
				$this->addHeader($attachment['data'] . self::LF);
			}
		}

		// final boundary, closes the headers
		$this->headers .= '--' . $boundary . '--';

		// return headers string
		return $this->headers;
	}


	/**
	 * Returns the output of the current SpoonEmail instance. This will only output information if you use SMTP to send mails.
	 *
	 * @return	string
	 */
	public function getOutput()
	{
		// debugging mode
		if($this->debug)
		{
			// SMTP enabled
			if($this->SMTP !== null) return $this->SMTP->getOutput();
		}
	}


	/**
	 * Returns the parsed content of a given template with optional variables.
	 *
	 * @return	string
	 * @param	string $template			The path to (including the filename for) the template.
	 * @param	array[optional] $variables	The variables to parse into $template.
	 */
	private function getTemplateContent($template, array $variables = null)
	{
		// check if compile directory is set
		if(empty($this->compileDirectory)) throw new SpoonEmailException('Compile directory is not set. Use setTemplateCompileDirectory.');

		// declare template
		$tpl = new SpoonTemplate();
		$tpl->setCompileDirectory($this->compileDirectory);
		$tpl->setForceCompile(true);

		// parse variables in the template if any are found
		if(!empty($variables)) $tpl->assign($variables);

		// return template content
		return $tpl->getContent($template);
	}


	/**
	 * Function to store the actual content for either HTML or plain text.
	 *
	 * @param	string $content			The body of the e-mail you wish to send.
	 * @param	array $variables		The variables to parse into the content.
	 * @param	string[optional] $type	The e-mail type. Either 'html' or 'plain'.
	 */
	private function processContent($content, $variables, $type = 'html')
	{
		// check for type
		$type = SpoonFilter::getValue($type, array('html', 'plain'), 'html');

		// exploded string
		$exploded = explode('/', str_replace('\\', '/', $content));
		$filename = end($exploded);

		// check if the string provided is a formatted as a file
		if(SpoonFilter::isFilename($filename) && preg_match('/^[\S]+\.\w{2,3}[\S]$/', $filename) && !strstr($filename, ' '))
		{
			// check if template exists
			if(!SpoonFile::exists($content)) throw new SpoonEmailException('Template not found. (' . $content . ')');

			// store content
			$this->content[$type] = (string) $this->getTemplateContent($content, $variables);
		}

		// string needs to be stored into a temporary file
		else
		{
			// set the name for the temporary file
			$tempFile = $this->compileDirectory . '/' . md5(uniqid()) . '.tpl';

			// write temp file
			SpoonFile::setContent($tempFile, $content);

			// store content
			$this->content[$type] = (string) $this->getTemplateContent($tempFile, $variables);

			// delete the temporary
			SpoonFile::delete($tempFile);
		}
	}


	/**
	 * Takes the name and e-mail in the given array and separates them with commas so they fit in a header.
	 *
	 * @return	string
	 * @param	array $recipients	The array with recipients to reformat into the correct string.
	 */
	private function reformatRecipientString(array $recipients)
	{
		// recipients found
		if(!empty($recipients))
		{
			// init var
			$string = '';

			// loop recipients
			foreach($recipients as $recipient)
			{
				// name should NOT be an e-mail address
				if(SpoonFilter::isEmail($recipient['name']))
				{
					throw new SpoonEmailException('E-mail addresses aren\'t allowed as names.');
				}

				// reformat to a proper string
				$stack = $recipient['name'] . ' <' . $recipient['email'] . '>';

				// just the email will do if no name is set
				if(empty($recipient['name'])) $stack = $recipient['email'];

				// add a comma as separator and store in new recipients stack
				$string .= $stack . ', ';
			}

			// return the reformatted string
			return mb_substr($string, 0, -2, SPOON_CHARSET);
		}
	}


	/**
	 * Attempts to send the actual email.
	 *
	 * @return	bool
	 */
	public function send()
	{
		// no recipients found
		if(empty($this->recipients)) throw new SpoonEmailException('Sending an email to no one is fairly silly. Add some recipients first.');

		// builds the headers for this email
		$headers = $this->getHeaders();

		// start with failed status
		$status = false;

		// check for mailmethod
		switch($this->method)
		{
			// send with SMTP protocol
			case 'smtp':
				// pass MAIL FROM command
				$this->SMTP->mailFrom($this->from['email'], $this->from['name']);

				// pass regular/CC/BCC recipients with RCPT TO command
				if(!empty($this->recipients)) foreach($this->recipients as $recipient) $this->SMTP->rcptTo($recipient['email']);
				if(!empty($this->CC)) foreach($this->CC as $recipient) $this->SMTP->rcptTo($recipient['email']);
				if(!empty($this->BCC)) foreach($this->BCC as $recipient) $this->SMTP->rcptTo($recipient['email']);

				// initiate SMTP send
				$status = $this->SMTP->send($headers);
			break;

			// send with PHP's native mail() function
			case 'mail':
				// send mail
				$status = mail($this->reformatRecipientString($this->recipients), $this->subject, null, $headers);
			break;

			// no one should be here
			default:
				throw new SpoonEmailException('Invalid mailmethod');
		}

		// clear the recipient lists and the headers
		unset($this->recipients, $this->CC, $this->BCC, $this->headers);

		// return status
		return $status;
	}


	/**
	 * Changes the charset from standard utf-8 to your preferred value.
	 *
	 * @param	string[optional] $charset	The charset to use, default is utf-8.
	 */
	public function setCharset($charset = 'utf-8')
	{
		$this->charset = ($charset !== null) ? SpoonFilter::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;
	}


	/**
	 * Set content transfer encoding.
	 * This is used for encoding the HTML and plaintext content.
	 *
	 * @param	string $encoding 	Encoding type. Possible values: 7bit, 8bit, base64, binary.
	 */
	public function setContentTransferEncoding($encoding)
	{
		$this->contentTransferEncoding = SpoonFilter::getValue($encoding, array('7bit', '8bit', 'base64', 'binary'), '8bit');
	}


	/**
	 * Sets the debug mode on/off.
	 *
	 * @param	bool[optional] $on	Should we enable debug-mode?
	 */
	public function setDebug($on = true)
	{
		$this->debug = (bool) $on;
	}


	/**
	 * Adds the sender information.
	 *
	 * @param	string $email			The sender's e-mail address.
	 * @param	string[optional] $name	The sender's name.
	 */
	public function setFrom($email, $name = null)
	{
		// check for valid email address
		if(!SpoonFilter::isEmail($email)) throw new SpoonEmailException('No valid email given.');

		// save the 'from' information
		$this->from['name'] = (string) $name;
		$this->from['email'] = (string) $email;
	}


	/**
	 * Sets the HTML content, which can be a template or just a string.
	 *
	 * @param	string $content				The HTML content for the e-mail.
	 * @param	array[optional] $variables	The variables to parse into the content.
	 */
	public function setHTMLContent($content, array $variables = null)
	{
		// check input
		if($content === null) throw new SpoonEmailException('No content string or template given.');

		// process content for html
		$this->processContent($content, $variables, 'html');
	}


	/**
	 * Sets the plain text content, which can be a template or just a string.
	 *
	 * @param	string $content				The plain text content for the e-mail.
	 * @param	array[optional] $variables	The variables to parse into the content.
	 */
	public function setPlainContent($content, array $variables = null)
	{
		// check input
		if($content === null) throw new SpoonEmailException('Template not found.');

		// process content for plain text
		$this->processContent($content, $variables, 'plain');
	}


	/**
	 * Sets the email priority level.
	 *
	 * @param	int[optional] $level	The e-mail's priority level (1-5, where 1 is not urgent).
	 */
	public function setPriority($level = 3)
	{
		// check input
		if(!SpoonFilter::isInteger($level) || !SpoonFilter::getValue($level, range(1, 5, 1), 3, 'int')) throw new SpoonEmailException('No valid priority level given, integer from 1 to 5 required.');

		// store priority level
		$this->priority = $level;
	}


	/**
	 * Sets the Reply-To header.
	 *
	 * @param	string $email			The e-mail address used in the reply-to header.
	 * @param	string[optional] $name	The name used in the reply-to header.
	 */
	public function setReplyTo($email, $name = null)
	{
		// check for valid email address
		if(!SpoonFilter::isEmail($email)) throw new SpoonEmailException('No valid email given.');

		// save the 'reply-to' information
		$this->replyTo[] = array('name' => (string) $name, 'email' => (string) $email);
	}


	/**
	 * Sets authentication info for the current SMTP connection.
	 *
	 * @param	string $username	The username to use.
	 * @param	string $password	The password to use.
	 */
	public function setSMTPAuth($username, $password)
	{
		// no smtp instance found
		if(!$this->SMTP) throw new SpoonEmailException('Make an SMTP connection first.');

		// push user and pass to the smtp object
		$this->SMTP->authenticate($username, $password);
	}


	/**
	 * Sets the SMTP connection.
	 *
	 * @param	string[optional] $host	The SMTP host we're connecting to.
	 * @param	int[optional] $port		The port we're connecting to.
	 * @param	int[optional] $timeout	The allowed timeout amount.
	 */
	public function setSMTPConnection($host = 'localhost', $port = null, $timeout = 30)
	{
		// redefine
		$host = preg_replace('/(\w)+:\/\//', '', (string) $host); // remove URL prefixes (like 'ssl://')
		$timeout = (int) $timeout;

		// set mailing method to smtp
		$this->method = 'smtp';

		// port was null
		if($port === null)
		{
			// check security level to change port
			switch($this->security)
			{
				case 'ssl':
					$port = 465;
				break;

				case 'tls':
					$port = 587;
				break;

				default:
					$port = 25;
			}
		}

		// store server information
		$this->SMTP = new SpoonEmailSMTP($host, $port, $timeout, $this->security);
	}


	/**
	 * Sets the SMTP security layer (either SSL or TLS)
	 *
	 * @param	string $layer	The security layer to use with SMTP (either SSL or TLS).
	 */
	public function setSMTPSecurity($layer)
	{
		// redefine
		$layer = (string) strtolower($layer);

		// security layer
		if($layer !== 'ssl' && $layer !== 'tls') throw new SpoonEmailException('Only SSL and TLS are available for SMTP.');

		// redefine
		$this->security = $layer;
	}


	/**
	 * Sets the email's subject header.
	 *
	 * @param	string $value	The subject for the e-mail.
	 */
	public function setSubject($value)
	{
		$this->subject = (string) $value;
	}


	/**
	 * Sets the email body template compile folder.
	 *
	 * @param	string $path	The directory specified here will be used to write compiled files in.
	 */
	public function setTemplateCompileDirectory($path)
	{
		$this->compileDirectory = (string) $path;
	}


	/**
	 * Sets the initial To: header to your liking, and thus masks a list of multiple recipients.
	 * This will have no effect if you don't use SMTP (the mail() function does not accept it).
	 *
	 * @param	string $name			The name used in the To: header.
	 * @param	string[optional] $email	The e-mail used in the To: header.
	 */
	public function setTo($name, $email = null)
	{
		// check input
		if(!SpoonFilter::isEmail($email)) throw new SpoonEmailException('Not a valid e-mail address for the TO header.');

		// save the 'to' information
		$this->to['name'] = (string) $name;
		$this->to['email'] = (string) $email;
	}
}


/**
 * This exception is used to handle email related exceptions.
 *
 * @package		spoon
 * @subpackage	email
 *
 *
 * @author		Dave Lens <dave@spoon-library.com>
 * @since		1.0.0
 */
class SpoonEmailException extends SpoonException {}
