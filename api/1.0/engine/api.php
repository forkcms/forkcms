<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * This class defines the API.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class API extends KernelLoader implements ApplicationInterface
{
	// statuses
	const OK = 200;
	const BAD_REQUEST = 400;
	const NOT_AUTHORIZED = 401;
	const FORBIDDEN = 403;
	const ERROR = 500;
	const NOT_FOUND = 404;

	/**
	 * @var string
	 */
	protected static $content;

	/**
	 * Initializes the entire API; extract class+method from the request, call, and output.
	 *
	 * This method exists because the service container needs to be set before
	 * the rest of API functionality gets loaded.
	 */
	public function initialize()
	{
		// simulate $_REQUEST
		$parameters = array_merge($_GET, $_POST);

		// validate parameters
		if(!isset($parameters['method']))
		{
			return self::output(self::BAD_REQUEST, array('message' => 'No method-parameter provided.'));
		}

		// check GET
		$method = SpoonFilter::getValue($parameters['method'], null, '');

		// validate
		if($method == '')
		{
			return self::output(self::BAD_REQUEST, array('message' => 'No method-parameter provided.'));
		}

		// process method
		$chunks = (array) explode('.', $method, 2);

		// validate method
		if(!isset($chunks[1]))
		{
			return self::output(self::BAD_REQUEST, array('message' => 'Invalid method.'));
		}

		// build the path to the backend API file
		if($chunks[0] == 'core') $path = BACKEND_CORE_PATH . '/engine/api.php';
		else $path = BACKEND_MODULES_PATH . '/' . $chunks[0] . '/engine/api.php';

		// check if the file is present? If it isn't present there is a problem
		if(!SpoonFile::exists($path)) return self::output(self::BAD_REQUEST, array('message' => 'Invalid method.'));

		// build config-object-name
		$className = 'Backend' . SpoonFilter::toCamelCase($chunks[0]) . 'API';
		$methodName = SpoonFilter::toCamelCase($chunks[1], '.', true);

		// require the class
		require_once $path;

		// validate if the method exists
		if(!is_callable(array($className, $methodName)))
		{
			return self::output(self::BAD_REQUEST, array('message' => 'Invalid method.'));
		}

		// call the method
		try
		{
			// init var
			$arguments = null;

			// create reflection method
			$reflectionMethod = new ReflectionMethod($className, $methodName);
			$parameterDocumentation = array();

			// get data from docs
			$matches = array();
			preg_match_all('/@param[\s\t]+(.*)[\s\t]+\$(.*)[\s\t]+(.*)$/Um', $reflectionMethod->getDocComment(), $matches);

			// documentation found
			if(!empty($matches[0]))
			{
				// loop matches
				foreach($matches[0] as $i => $row)
				{
					// set documentation
					$parameterDocumentation[$matches[2][$i]] = array(
						'type' => str_replace('[optional]', '', $matches[1][$i]),
						'optional' => (substr_count($matches[1][$i], '[optional]') > 0),
						'description' => $matches[3][$i]
					);
				}
			}

			// loop parameters
			foreach($reflectionMethod->getParameters() as $parameter)
			{
				// init var
				$name = $parameter->getName();

				// check if the parameter is available
				if(!$parameter->isOptional() && !isset($parameters[$name]))
				{
					return self::output(self::BAD_REQUEST, array('message' => 'No ' . $name . '-parameter provided.'));
				}

				// add not-passed arguments
				if($parameter->isOptional() && !isset($parameters[$name])) $arguments[] = $parameter->getDefaultValue();

				// add argument if we know the type
				elseif(isset($parameterDocumentation[$name]['type']))
				{
					// get default value
					$defaultValue = null;
					if($parameter->isOptional()) $defaultValue = $parameter->getDefaultValue();

					// add argument
					$arguments[] = SpoonFilter::getValue(
						$parameters[$name],
						null,
						$defaultValue,
						$parameterDocumentation[$name]['type']
					);
				}

				// fallback
				else $arguments[] = $parameters[$name];
			}

			// get the return
			$data = (array) call_user_func_array(array($className, $methodName), (array) $arguments);

			// output
			if(self::$content === null)
			{
				self::output(self::OK, $data);
			}
		}

		// catch exceptions
		catch(Exception $e)
		{
			// if we are debugging we should see the exceptions
			if(SPOON_DEBUG)
			{
				if(isset($parameters['debug']) && $parameters['debug'] == 'false')
				{
					// do nothing
				}
				else throw $e;
			}

			// output
			return self::output(500, array('message' => $e->getMessage()));
		}
	}

	/**
	 * Callback-method for elements in the return-array
	 *
	 * @param mixed $input The value.
	 * @param string $key The key.
	 * @param DOMElement $XML The root-element.
	 */
	private static function arrayToXML(&$input, $key, $XML)
	{
		// skip attributes
		if($key == '@attributes') return;

		// create element
		$element = new DOMElement($key);

		// append
		$XML->appendChild($element);

		// no value? just stop here
		if($input === null) return;

		// is it an array and are there attributes
		if(is_array($input) && isset($input['@attributes']))
		{
			// loop attributes
			foreach((array) $input['@attributes'] as $name => $value) $element->setAttribute($name, $value);

			// remove attributes
			unset($input['@attributes']);

			// reset the input if it is a single value
			if(count($input) == 1)
			{
				// get keys
				$keys = array_keys($input);

				// reset
				$input = $input[$keys[0]];
			}
		}

		// the input isn't an array
		if(!is_array($input))
		{
			// a string?
			if(is_string($input))
			{
				// characters that require a cdata wrapper
				$illegalCharacters = array('&', '<', '>', '"', '\'');

				// default we don't wrap with cdata tags
				$wrapCdata = false;

				// find illegal characters in input string
				foreach($illegalCharacters as $character)
				{
					if(stripos($input, $character) !== false)
					{
						// wrap input with cdata
						$wrapCdata = true;

						// no need to search further
						break;
					}
				}

				// check if value contains illegal chars, if so wrap in CDATA
				if($wrapCdata) $element->appendChild(new DOMCdataSection($input));

				// just regular element
				else $element->appendChild(new DOMText($input));
			}

			// regular element
			else $element->appendChild(new DOMText($input));
		}

		// the value is an array
		else
		{
			// init var
			$isNonNumeric = false;

			// loop all elements
			foreach($input as $index => $value)
			{
				// non numeric string as key?
				if(!is_numeric($index))
				{
					// reset var
					$isNonNumeric = true;

					// stop searching
					break;
				}
			}

			// is there are named keys they should be handles as elements
			if($isNonNumeric) array_walk($input, array('API', 'arrayToXML'), $element);

			// numeric elements means this a list of items
			else
			{
				// handle the value as an element
				foreach($input as $value)
				{
					if(is_array($value)) array_walk($value, array('API', 'arrayToXML'), $element);
				}
			}
		}
	}

	/**
	 * Default authentication
	 *
	 * @return bool
	 */
	public static function authorize()
	{
		// grab data
		$email = SpoonFilter::getGetValue('email', null, '');
		$nonce = SpoonFilter::getGetValue('nonce', null, '');
		$secret = SpoonFilter::getGetValue('secret', null, '');

		// data can be available in the POST, so check it
		if($email == '') $email = SpoonFilter::getPostValue('email', null, '');
		if($nonce == '') $nonce = SpoonFilter::getPostValue('nonce', null, '');
		if($secret == '') $secret = SpoonFilter::getPostValue('secret', null, '');

		// check if needed elements are available
		if($email === '' || $nonce === '' || $secret === '')
		{
			return self::output(
				self::NOT_AUTHORIZED,
				array('message' => 'Not authorized.')
			);
		}

		// get the user
		$user = new BackendUser(null, $email);

		// user is god!
		if($user->isGod()) return true;

		// get settings
		$apiAccess = $user->getSetting('api_access', false);
		$apiKey = $user->getSetting('api_key');

		// no API-access
		if(!$apiAccess)
		{
			return self::output(
				self::FORBIDDEN,
				array('message' => 'Your account isn\'t allowed to use the API. Contact an administrator.')
			);
		}

		// create hash
		$hash = BackendAuthentication::getEncryptedString($email . $apiKey, $nonce);

		// output
		if($secret != $hash) return self::output(self::FORBIDDEN, array('message' => 'Invalid secret.'));

		// return
		return true;
	}

	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function display()
	{
		return new Response(
			self::$content, 200
		);
	}

	/**
	 * This is called in backend/modules/<module>/engine/api.php to limit certain calls to
	 * a given request method.
	 *
	 * @param string $method
	 * @return bool
	 */
	public static function isValidRequestMethod($method)
	{
		if($method !== $_SERVER['REQUEST_METHOD'])
		{
			$message = 'Illegal request method, only ' . $method . ' allowed for this method';
			return self::output(self::BAD_REQUEST, array('message' => $message));
		}

		return true;
	}

	/**
	 * Output the return
	 *
	 * @param int $statusCode The status code.
	 * @param array[optional] $data The data to return.
	 */
	public static function output($statusCode, array $data = null)
	{
		// get output format
		$allowedFormats = array('xml', 'json');

		// use XML as a default
		$output = 'xml';

		// use the accept header if it is provided
		if(isset($_SERVER['HTTP_ACCEPT']))
		{
			$acceptHeader = strtolower($_SERVER['HTTP_ACCEPT']);
			if(substr_count($acceptHeader, 'text/xml') > 0) $output = 'xml';
			if(substr_count($acceptHeader, 'application/xml') > 0) $output = 'xml';
			if(substr_count($acceptHeader, 'text/json') > 0) $output = 'json';
			if(substr_count($acceptHeader, 'application/json') > 0) $output = 'json';
		}

		// format specified as a GET-parameter will overrule the one provided through the accept headers
		$output = SpoonFilter::getGetValue('format', $allowedFormats, $output);

		// if the format was specified in the POST it will overrule all previous formats
		$output = SpoonFilter::getPostValue('format', $allowedFormats, $output);

		// return in the requested format
		switch($output)
		{
			// json
			case 'json':
				self::outputJSON($statusCode, $data);
				break;

			// xml
			default:
				self::outputXML($statusCode, $data);
		}

		return ($statusCode === 200);
	}

	/**
	 * Output as JSON
	 *
	 * @param int $statusCode The status code.
	 * @param array[optional] $data The data to return.
	 */
	private static function outputJSON($statusCode, array $data = null)
	{
		// redefine
		$statusCode = (int) $statusCode;

		// init vars
		$pathChunks = explode('/', trim(dirname(__FILE__), '/'));
		$version = $pathChunks[count($pathChunks) - 2];

		// build array
		$JSON = array();
		$JSON['meta']['status_code'] = $statusCode;
		$JSON['meta']['status'] = ($statusCode === 200) ? 'ok' : 'error';
		$JSON['meta']['version'] = FORK_VERSION;
		$JSON['meta']['endpoint'] = SITE_URL . '/api/' . $version;

		// add data
		if($data !== null) $JSON['data'] = $data;

		// set correct headers
		SpoonHTTP::setHeadersByCode($statusCode);
		SpoonHTTP::setHeaders('content-type: application/json;charset=' . SPOON_CHARSET);

		// output JSON
		self::$content = json_encode($JSON);
	}

	/**
	 * Output as XML
	 *
	 * @param int $statusCode The status code.
	 * @param array[optional] $data The data to return.
	 */
	private static function outputXML($statusCode, array $data = null)
	{
		// redefine
		$statusCode = (int) $statusCode;

		// init vars
		$pathChunks = explode('/', trim(dirname(__FILE__), '/'));
		$version = $pathChunks[count($pathChunks) - 2];

		// init XML
		$XML = new DOMDocument('1.0', SPOON_CHARSET);

		// set some properties
		$XML->preserveWhiteSpace = false;
		$XML->formatOutput = true;

		// create root element
		$root = $XML->createElement('fork');

		// add attributes
		$root->setAttribute('status_code', $statusCode);
		$root->setAttribute('status', ($statusCode == 200) ? 'ok' : 'error');
		$root->setAttribute('version', FORK_VERSION);
		$root->setAttribute('endpoint', SITE_URL . '/api/' . $version);

		// append
		$XML->appendChild($root);

		// build XML
		array_walk($data, array('API', 'arrayToXML'), $root);

		// set correct headers
		SpoonHTTP::setHeadersByCode($statusCode);
		SpoonHTTP::setHeaders('content-type: text/xml;charset=' . SPOON_CHARSET);

		// output XML
		self::$content = $XML->saveXML();
	}
}
