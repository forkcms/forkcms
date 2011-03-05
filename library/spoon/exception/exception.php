<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	exception
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */


/**
 * This is the default spoon exception, that extends the default php exception
 *
 * @package		spoon
 * @subpackage	exception
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonException extends Exception
{
	/**
	 * Exception name
	 *
	 * @var	string
	 */
	protected $name;


	/**
	 * String to obfuscate
	 *
	 * @var	array
	 */
	protected $obfuscate = array();


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $message				The message that should be used.
	 * @param	int[optional] $code			A numeric code for the exceptions.
	 * @param	mixed[optional] $obfuscate	The string(s) that will be obfuscated.
	 */
	public function __construct($message, $code = 0, $obfuscate = null)
	{
		// parent constructor
		parent::__construct((string) $message, (int) $code);

		// set name
		$this->name = get_class($this);

		// obfuscating?
		if($obfuscate !== null) $this->obfuscate = (array) $obfuscate;
	}


	/**
	 * Retrieve the name of this exception.
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Return an array of elements that need to be obfuscated.
	 *
	 * @return	array
	 */
	public function getObfuscate()
	{
		return $this->obfuscate;
	}
}


// redefine the exception handler
set_exception_handler('exceptionHandler');


/**
 * Prints out the thrown exception in a more readable manner
 *
 * @return	void
 * @param	SpoonException $exception
 */
function exceptionHandler($exception)
{
	// fetch trace stack
	$trace = $exception->getTrace();

	// specific name
	$name = (is_callable(array($exception, 'getName'))) ? $exception->getName() : 'UnknownException';

	// spoon type exception
	if(is_callable(array($exception, 'getName')) && strtolower(substr($exception->getName(), 0, 5)) == 'spoon' && $exception->getCode() != 0)
	{
		$documentation = '&raquo; <a href="http://www.spoon-library.com/exceptions/detail/' . $exception->getCode() . '">view documentation</a>';
	}

	// request uri?
	if(!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST'] = '';
	if(!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '';

	// user agent
	$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';

	// generate output
	$output = '
	<html>
		<head>
			<title>' . $name . '</title>
		</head>
		<body style="background-color: #F2F2F2; color: #0000000, font-family: Verdana, Tahoma, Arial; font-size 10px; margin: 0; padding: 0;">
			<table width="100%">
				<tr>
					<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">&nbsp;</td>
					<td style="width: 550px">
						<table width="550px;">
							<tr>
								<td style="background-color: #EEEEEE; border: 1px solid #B2B2B2;">
									<h1 style="font-size: 12px; margin: 5px 5px 12px 5px; padding: 0 0 5px 0; color: #000000; font-family: Verdana, Tahoma, Arial; border-bottom: 1px solid #999999;">' . $name . ' &raquo; Main</h1>
									<table width="550px">
										<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Message</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . $exception->getMessage() . '</td>
										</tr>
										<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">File</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . wordwrap($exception->getFile(), 70, '<br />', true) . '</td>
										</tr>
										<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Line</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . $exception->getLine() . '</td>
										</tr>
										<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Date</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . date('r') . '</td>
										</tr>
										<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">URL</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . "\n";
		// request URL
		$output .= '							<a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '</a>' . "\n";
		$output .= '						</td>
										</tr>
										<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Referring URL</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . "\n";
		// referring URL
		if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '')
		{
			$output .= '						<a href="' . $_SERVER['HTTP_REFERER'] . '">' . $_SERVER['HTTP_REFERER'] . '</a>' . "\n";
		}
		else $output .= '						Unknown Referrer' . "\n";

		$output .= '						</td>
										</tr>
										<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Request Method</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . $_SERVER['REQUEST_METHOD'] . '</td>
										</tr>
										<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">User-agent</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . $userAgent . '</td>
										</tr>';
		// no documentation ?
		if(isset($documentation))
		{
			$output .= '				<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Documentation</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . $documentation . '</td>
										</tr>';
		}

		// we know about the last error
		if(error_get_last() !== null)
		{
			// define message
			$error = error_get_last();

			// show output
			$output .= '				<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">PHP error</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . $error['message'] . '</td>
										</tr>';
		}

		// continue output
		$output .= '				</table>
								</td>
							<tr>
							<tr>
								<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">&nbsp;</td>
							</tr>
							<!-- variables -->
							<tr>
								<td style="background-color: #EEEEEE; border: 1px solid #B2B2B2;">
									<h1 style="font-size: 12px; margin: 5px 5px 12px 5px; padding: 0 0 5px 0; color: #000000; font-family: Verdana, Tahoma, Arial; border-bottom: 1px solid #999999;">' . $name . ' &raquo; Variables</h1>
									<table width="550px;">' . "\n";

		// $_GET has items
		if(isset($_GET))
		{
			$output .= '				<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">$_GET</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">
												<pre style="font-family: Courier; margin-bottom: 10px;">' . print_r($_GET, true) . '</pre>
											</td>
										</tr>' . "\n";
		}

		// $_POST has items
		if(isset($_POST))
		{
			$output .= '				<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">$_POST</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">
												<pre style="font-family: Courier; margin-bottom: 10px;">' . print_r($_POST, true) . '</pre>
											</td>
										</tr>';
		}

		// $_SESSION has items
		if(isset($_SESSION))
		{
			$output .= '				<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">$_SESSION</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">
												<pre style="font-family: Courier; margin-bottom: 10px;">' . print_r($_SESSION, true) . '</pre>
											</td>
										</tr>';
		}

		// $_COOKIE has items
		if(isset($_COOKIE))
		{
			$output .= '				<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">$_COOKIE</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">
												<pre style="font-family: Courier; margin-bottom: 10px;">' . print_r($_COOKIE, true) . '</pre>
											</td>
										</tr>';
		}

		// $_FILES has items
		if(isset($_FILES))
		{
			$output .= '				<tr>
											<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">$_FILES</th>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">
												<pre style="font-family: Courier; margin-bottom: 10px;">' . print_r($_FILES, true) . '</pre>
											</td>
										</tr>';
		}

		$output .= '				</table>
								</td>
							</tr>
							<tr>
								<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">&nbsp;</td>
							</tr>
							<!-- stack -->
							<tr>
								<td style="background-color: #EEEEEE; border: 1px solid #B2B2B2;">
									<h1 style="font-size: 12px; margin: 5px 5px 12px 5px; padding: 0 0 5px 0; color: #000000; font-family: Verdana, Tahoma, Arial; border-bottom: 1px solid #999999;">' . $name . ' &raquo; Trace</h1>
									<table width="550px;">' . "\n";

		// trace has items
		if(count($exception->getTrace()) != 0)
		{
			// fetch entire stack
			$entireTraceStack = $exception->getTrace();

			// loop elements
			foreach($entireTraceStack as $traceStack)
			{
				// open defintion list
				$output .= '			<tr>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">
												<table width="550px;">
													<tr>
														<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">File</th>
														<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . ((isset($traceStack['file'])) ? wordwrap($traceStack['file'], 70, '<br />', true) : 'Unknown') . '
														</td>
													</tr>
													<tr>
														<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Line</th>
														<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . ((isset($traceStack['line'])) ? $traceStack['line'] : 'Unknown') . '
														</td>
													</tr>';

				// class & function
				if(isset($traceStack['class']))
				{
					$output .= '					<tr>
														<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Class</th>
														<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . $traceStack['class'] . '
														</td>
													</tr>';
				}
				if(isset($traceStack['function']))
				{
					$output .= '					<tr>
														<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Function</th>
														<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">' . $traceStack['function'] . '													</td>
													</tr>';
				}

				// function arguments
				if(isset($traceStack['args']) && count($traceStack['args']) != 0)
				{
					// argument title
					$output .= '					<tr>
														<th width="110px" style="vertical-align: top; text-align: left; font-weight: 700; padding: 0 0 0 10px; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">Argument(s)</th>
														<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">
															<pre style="font-family: Courier; margin-bottom: 10px;">' . print_r($traceStack['args'], true) . '</pre>
														</td>
													</tr>';
				}

				// close defintion list
				$output .= '					</table>
											</td>
										</tr>';
			}
		}

		// no trace
		else $output .= '				<tr>
											<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">No trace available.</td>
										</tr>';
		// continue output generation
		$output .= '				</table>
								</td>
							</tr>
						</table>
					</td>
					<td style="vertical-align: top; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #000000;">&nbsp;</td>
				</tr>
			</table>
		</body>
	</html>
	';

	// obfuscate
	if(is_callable(array($exception, 'getObfuscate')) && count($exception->getObfuscate()) != 0)
	{
		$output = str_replace($exception->getObfuscate(), '***', $output);
	}

	// custom callback?
	if(SPOON_EXCEPTION_CALLBACK != '')
	{
		// function
		if(!strpos(SPOON_EXCEPTION_CALLBACK, '::'))
		{
			// function actually has been defined
			if(function_exists(SPOON_EXCEPTION_CALLBACK))
			{
				call_user_func_array(SPOON_EXCEPTION_CALLBACK, array($exception, $output));
			}

			// something went wrong
			else exit('The function stored in SPOON_EXCEPTION_CALLBACK (' . SPOON_EXCEPTION_CALLBACK . ') could not be found.');
		}

		// method
		else
		{
			// method
			$method = explode('::', SPOON_EXCEPTION_CALLBACK);

			// 2 parameters and exists
			if(count($method) == 2 && is_callable(array($method[0], $method[1])))
			{
				call_user_func_array(array($method[0], $method[1]), array($exception, $output));
			}

			// something went wrong
			else exit('The method stored in SPOON_EXCEPTION_CALLBACK (' . SPOON_EXCEPTION_CALLBACK . ') cound not be found.');
		}

	}

	// default exception handling
	else
	{
		// debugging enabled (show output)
		if(SPOON_DEBUG) echo $output;

		// debugging disabled
		else echo SPOON_DEBUG_MESSAGE;
	}

	// mail it?
	if(SPOON_DEBUG_EMAIL != '')
	{
		// e-mail headers
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-15\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: Normal\n";
		$headers .= "X-Mailer: SpoonLibrary Webmail\n";
		$headers .= "From: Spoon Library <no-reply@spoon-library.com>\n";

		// send email
		@mail(SPOON_DEBUG_EMAIL, 'Exception Occured', $output, $headers);
	}

	// stop script execution
	exit;
}

?>