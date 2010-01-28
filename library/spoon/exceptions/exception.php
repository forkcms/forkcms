<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		errors
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/**
 * This is the default spoon exception, that extends the default php exception
 *
 * @package		exceptions
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
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

	// class & function exist and are spoon related
	if(isset($trace[0]['class']) && isset($trace[0]['function']) && strtolower(substr($trace[0]['class'], 0, 5)) == 'spoon')
	{
		$documentationURL = strtolower($trace[0]['class']) .'/'. strtolower($trace[0]['function']);
		$documentation = '&raquo; <a href="http://docs.spoon-library.be/'. $documentationURL .'">view documentation</a>';
	}

	// specific name
	$name = (method_exists($exception, 'getName')) ? $exception->getName() : 'UnknownException';

	// request uri?
	if(!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST'] = '';
	if(!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '';

	// generate output
	$output = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title>'. $name .'</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<style type="text/css">
			body
			{
				background-color: #f2f2f2;
				color: #000;
				font-family: verdana, tahoma, arial;
				font-size: 10px;
				margin: 10px;
				padding: 0;
			}

			#container
			{
				margin: 0 auto;
				width: 550px;
			}

			#container-main, #container-stack, #container-variables
			{
				background-color: #eee;
				border: 1px solid #b2b2b2;
				margin: 0 0 10px 0;
			}

			#main, #stack, #variables
			{
				margin: 10px 10px 10px 10px;
			}

			#main h1, #stack h1, #variables h1
			{
				font-size: 12px;
				margin: 0 0 10px 0;
				padding: 0;
			}

			#main dl, #stack dl, #variables dl
			{
				border-top: 1px solid #999;
				margin: 0;
				padding: 5px 0 0 0;
			}

			#main dt, #stack dt, #variables dt
			{
				float: left;
				font-weight: bold;
				margin: 0;
				padding: 0;
				text-align: right;
			}

			#main dd, #stack dd, #variables dd
			{
				margin: 0 0 5px 100px;
				padding: 0;
			}

			#main dd pre, #stack dd pre, #variables dd pre
			{
				font-family: verdana, tahoma, arial;
				font-size: 10px;
				margin: 0;
				padding: 0;
			}
			</style>
		</head>

		<body>
			<div id="container">

				<!-- main -->
				<div id="container-main">
					<div id="main">
						<h1>'. $name .' &raquo; Main</h1>
						<dl>
							<dt>Message</dt>
								<dd>'. $exception->getMessage() .'</dd>
							<dt>File</dt>
								<dd>'. wordwrap($exception->getFile(), 70, '<br />', true) .'</dd>
							<dt>Line</dt>
								<dd>'. $exception->getLine() .'</dd>
							<dt>Date</dt>
								<dd>'. date('r') .'</dd>
							<dt>URL</dt>
								<dd>';

								// request url
								$output .= '<a href="http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] .'">http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] .'</a>';
								$output .= '</dd>
							<dt>Referring URL</dt>
								<dd>';

								// referring url
								$output .= (isset($_SERVER['HTTP_REFERER'])) ? '<a href="'. $_SERVER['HTTP_REFERER'] .'">'. $_SERVER['HTTP_REFERER'] .'</a>' : 'Unknown Referrer';
								$output .= '</dd>
							<dt>Request Method</dt>
								<dd>'. $_SERVER['REQUEST_METHOD'] .'</dd>';

								// no documentation ?
								if(isset($documentation))
								{
									$output .= '<dt>Documentation</dt>
													<dd>'. $documentation .'</dd>';
								}

							// continue output
							$output .= '
						</dl>
					</div>
				</div>

				<!-- variables -->
					<div id="container-variables">
						<div id="variables">
							<h1>'. $name .' &raquo; Variables</h1>';


								// $_GET has items
								if(isset($_GET))
								{
									// open defition list
									$output .= "<dl>\r\n";

									// title + array
									$output .= "<dt>\$_GET</dt>\r\n<dd><pre>". print_r($_GET, true) ."</pre></dd>\r\n";

									// close definition list
									$output .= "</dl>\r\n";
								}

								// $_POST has items
								if(isset($_POST))
								{
									// open defition list
									$output .= "<dl>\r\n";

									// title + array
									$output .= "<dt>\$_POST</dt>\r\n<dd><pre>". print_r($_POST, true) ."</pre></dd>\r\n";

									// close definition list
									$output .= "</dl>\r\n";
								}

								// $_SESSION has items
								if(isset($_SESSION))
								{
									// open defition list
									$output .= "<dl>\r\n";

									// title + array
									$output .= "<dt>\$_SESSION</dt>\r\n<dd><pre>". print_r($_SESSION, true) ."</pre></dd>\r\n";

									// close definition list
									$output .= "</dl>\r\n";
								}

								// $_COOKIE has items
								if(isset($_COOKIE))
								{
									// open defition list
									$output .= "<dl>\r\n";

									// title + array
									$output .= "<dt>\$_COOKIE</dt>\r\n<dd><pre>". print_r($_COOKIE, true) ."</pre></dd>\r\n";

									// close definition list
									$output .= "</dl>\r\n";
								}

								// $_FILES has items
								if(isset($_FILES))
								{
									// open defition list
									$output .= "<dl>\r\n";

									// title + array
									$output .= "<dt>\$_FILES</dt>\r\n<dd><pre>". print_r($_FILES, true) ."</pre></dd>\r\n";

									// close definition list
									$output .= "</dl>\r\n";
								}

							$output .= '
						</div>
					</div>

				<!-- stack -->
				<div id="container-stack">
					<div id="stack">
						<h1>'. $name .' &raquo; Trace</h1>';

							// trace has items
							if(count($exception->getTrace()) != 0)
							{
								// fetch entire stack
								$entireTraceStack = $exception->getTrace();

								// loop elements
								foreach($entireTraceStack as $traceStack)
								{
									// open defintion list
									$output .= "<dl>\r\n";

									// file & line
									$output .= "<dt>File</dt>\r\n";
									$output .= '<dd>'. ((isset($traceStack['file'])) ? wordwrap($traceStack['file'], 70, '<br />', true) : 'Unknown') ."</dd>\r\n";
									$output .= "<dt>Line</dt>\r\n";
									$output .= '<dd>'. ((isset($traceStack['line'])) ? $traceStack['line'] : 'Unknown') ."</dd>\r\n";

									// class & function
									if(isset($traceStack['class'])) $output .= "<dt>Class</dt>\r\n<dd>". $traceStack['class'] ."</dd>\r\n";
									if(isset($traceStack['function'])) $output .= "<dt>Function</dt>\r\n<dd>". $traceStack['function'] ."</dd>\r\n";

									// function arguments
									if(isset($traceStack['args']) && count($traceStack['args']) != 0)
									{
										// argument title
										$output .= "<dt>Argument(s)</dt>\r\n<dd><pre>". print_r($traceStack['args'], true) ."</pre></dd>\r\n";
									}

									// close defintion list
									$output .= "</dl>\r\n";
								}
							}

							// no trace
							else $output .= 'No trace available.';

							// continue output generation
							$output .= '
						</div>
					</div>
				</div>
			</body>
		</html>
		';

	// obfuscate
	if(method_exists($exception, 'getObfuscate') && count($exception->getObfuscate()) != 0)
	{
		$output = str_replace($exception->getObfuscate(), '***', $output);
	}

	// debugging enabled (show output)
	if(SPOON_DEBUG) echo $output;

	// debugging disabled
	else echo SPOON_DEBUG_MESSAGE;

	// mail it?
	if(SPOON_DEBUG_EMAIL != '')
	{
		// e-mail headers
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-15\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: Normal\n";
		$headers .= "X-Mailer: SpoonLibrary Webmail\n";
		$headers .= "From: Spoon Library <no-reply@spoon-library.be>\n";

		// send email
		@mail(SPOON_DEBUG_EMAIL, 'Exception Occured', $output, $headers);
	}

	// stop script execution
	exit;
}

?>