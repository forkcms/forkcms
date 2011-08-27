<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	xmlrpc
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.1.4
 */


/**
 * This base class provides all the methods used by an XMLRPC-client.
 *
 * @package		spoon
 * @subpackage	xmlrpc
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.1.4
 */
class SpoonXMLRPCClient
{
	/**
	 * The headers
	 *
	 * @var	array
	 */
	private $headers = array();


	/**
	 * The port
	 *
	 * @var	int
	 */
	private $port = 80;


	/**
	 * The server
	 *
	 * @var	string
	 */
	private $server;


	/**
	 * The timeout in seconds
	 *
	 * @var	int
	 */
	private $timeout = 10;


	/**
	 * The user-agent
	 *
	 * @var	string
	 */
	private $userAgent;


	/**
	 * Default constructor
	 *
	 * @param	string $server	The server to communicate with.
	 */
	public function __construct($server)
	{
		$this->setServer($server);
	}


	/**
	 * Build XML for a value
	 *
	 * @return	string
	 * @param	array $parameter	The parameter(s) to build.
	 */
	private function buildValueXML(array $parameter)
	{
		// each type has his own XML
		switch($parameter['type'])
		{
			// array
			case 'array':
				// start
				$xml = '<array>' . "\n";
				$xml .= '	<data>' . "\n";

				// loop values
				foreach($parameter['value'] as $item) $xml .= '		<value>' . $this->buildValueXML($item) . "</value>\n";

				// end
				$xml .= '	</data>' . "\n";
				$xml .= '</array>';

				// return
				return $xml;
			break;

			// base64
			case 'base64':
				return '<base64>' . (string) $parameter['value'] . '</base64>';
			break;

			// boolean
			case 'boolean':
				return ((bool) $parameter['value']) ? '<boolean>1</boolean>' : '<boolean>0</boolean>';
			break;

			// date/time
			case 'date/time':
				if(is_integer($parameter['value'])) $parameter['value'] = date('c', (int) $parameter['value']);
				return '<dateTime.iso8601>' . (string) $parameter['value'] . '</dateTime.iso8601>';
			break;

			// double
			case 'double':
				return '<double>' . (float) $parameter['value'] . '</double>';
			break;

			// int
			case 'int':
				return '<int>' . (int) $parameter['value'] . '</int>';
			break;

			// i4
			case 'i4':
				return '<i4>' . (int) $parameter['value'] . '</i4>';
			break;

			// string
			case 'string':
				return '<string>' . (string) $parameter['value'] . '</string>';
			break;

			// struct
			case 'struct':
				// start
				$xml = '<struct>' . "\n";

				// loop values
				foreach($parameter['value'] as $item)
				{
					$xml .= '	<member>' . "\n";
					$xml .= '		<name>' . (string) $item['name'] . '</name>' . "\n";
					$xml .= '		<value>' . $this->buildValueXML($item) . '</value>' . "\n";
					$xml .= '	</member>' . "\n";
				}

				// end
				$xml .= '</struct>';

				// return
				return $xml;
			break;

			// nil
			case 'nil':
				return '<nil/>';
			break;

			// unknwon type
			default:
				throw new SpoonXMLRPCException('Invalid type (' . $parameter['type'] . ').');
		}
	}


	/**
	 * Build the XML to send
	 *
	 * @return	string
	 * @param	string $method					The method that should be called.
	 * @param	array[optional] $parameters		The parameters.
	 */
	private function buildXML($method, array $parameters = array())
	{
		// redefine
		$method = (string) $method;

		// init var
		$xml = '<?xml version="1.0"?>' . "\n";
		$xml .= '<methodCall>' . "\n";

		// add method
		$xml .= '	<methodName>' . (string) $method . '</methodName>' . "\n";

		if(!empty($parameters))
		{
			// start parameters
			$xml .= '	<params>' . "\n";

			// loop parameters and build parameters
			foreach($parameters as $parameter) $xml .= '		<param>' . $this->buildValueXML($parameter) . '</param>' . "\n";

			// end parameters
			$xml .= '	</params>' . "\n";
		}

		// end XML
		$xml .= '</methodCall>' . "\n";

		// return
		return $xml;
	}


	/**
	 * Decode XMLRPC-response
	 *
	 * @return	mixed
	 * @param	SimpleXMLElement $xml	The element to decode.
	 */
	private function decode($xml)
	{
		// validate
		if(!isset($xml->params->param)) throw new SpoonXMLRPCException('Invalid response.');

		// init var
		$return = array();

		// loop params
		foreach($xml->params->param as $param)
		{
			// decode value, and add it to the retuen array
			$return[] = $this->decodeValue($param->value);
		}

		// if there is just one param we return it at once
		if(count($return) == 1) return $return[0];

		// fallback (multiple params in response)
		return $return;
	}


	/**
	 * Decode XMLRPC fault
	 *
	 * @return	array
	 * @param	SimpleXMLElement $xml	The Fault-element to decode.
	 */
	private function decodeFaultXML($xml)
	{
		// validate
		if(!isset($xml->value)) return false;

		// decode
		return $this->decodeValue($xml->value);
	}


	/**
	 * Decode a value
	 *
	 * @return	mixed
	 * @param	SimpleXMLElement $xml	The value to be decoded.
	 */
	private function decodeValue($xml)
	{
		// loop children
		foreach($xml->children() as $element)
		{
			// get type
			$type = $element->getName();

			// decode correct
			switch($type)
			{
				// array
				case 'array':
					// init
					$return = array();

					// loop members
					foreach($element->data as $data) $return[] = $this->decodeValue($data->value);

					// return
					return $return;
				break;

				// base64
				case 'base64':
					return base64_decode((string) $element);
				break;

				// boolean
				case 'boolean':
					return (bool) ((string) $element == '1');
				break;

				// date/time
				case 'dateTime.iso8601':
					// convert to UNIX-timestamp
					$value = strtotime((string) $element);

					// validate timestamp
					if($value == 0) $value = (string) $element;

					// return value
					return $value;
				break;

				// double
				case 'double':
					return (float) $element;
				break;

				// int
				case 'int':
					return (int) $element;
				break;

				// i4
				case 'i4':
					return (int) $element;
				break;

				// string
				case 'string':
					return (string) $element;
				break;

				// struct
				case 'struct':
					// init
					$return = array();

					// loop members
					foreach($element->member as $member)
					{
						$name = (string) $member->name;
						$value = $this->decodeValue($member->value);

						// add
						$return[$name] = $value;
					}

					// return
					return $return;
				break;

				// nil
				case 'nil':
					return null;
				break;

				default:
					throw new SpoonXMLRPCException('Invalid type (' . $type . ').');
			}
		}
	}


	/**
	 * Make the call.
	 *
	 * @return	string
	 * @param	string $method					The method to call.
	 * @param	array[optional] $parameters		The parameters to pass.
	 */
	public function execute($method, array $parameters = null)
	{
		// check if curl is available
		if(!function_exists('curl_init')) throw new SpoonFileException('This method requires cURL (http://php.net/curl), it seems like the extension isn\'t installed.');

		// redefine
		$method = (string) $method;
		$parameters = (array) $parameters;

		// possible parameter types
		$allowedTypes = array('array', 'base64', 'boolean', 'date/time', 'double', 'int', 'i4', 'string', 'struct', 'nil');

		// validate
		foreach($parameters as $parameter)
		{
			// validate if needed stuff is available
			if(!isset($parameter['type'])) throw new SpoonXMLRPCException('Invalid parameter. A parameter needs type and value as keys.');
			if($parameter['type'] !== 'nil' && !isset($parameter['value'])) throw new SpoonXMLRPCException('Invalid parameter. A parameter needs type and value as keys.');

			// validate type
			if(!in_array($parameter['type'], $allowedTypes)) throw new SpoonXMLRPCException('Invalid parameter-type (' . $parameter['type'] . '). Possible types are: ' . implode(', ', $allowedTypes) . '.');

			// extra checks for array-type
			if($parameter['type'] == 'array')
			{
				// loop values
				foreach((array) $parameter['value'] as $value)
				{
					// is needed stuff available?
					if(!isset($value['type']) || !isset($value['value'])) throw new SpoonXMLRPCException('Invalid parameter, an array-type needs the value to be an array wherein each item needs type and value as keys.');

					// validate types
					if(!in_array($value['type'], $allowedTypes)) throw new SpoonXMLRPCException('Invalid parameter-type (' . $value['type'] . '). Possible types are: ' . implode(', ', $allowedTypes) . '.');
				}
			}

			// extra checks for struct type
			if($parameter['type'] == 'struct')
			{
				// loop values
				foreach((array) $parameter['value'] as $value)
				{
					// is needed stuff available?
					if(!isset($value['type']) || !isset($value['value']) || !isset($value['name'])) throw new SpoonXMLRPCException('Invalid parameter, a struct-type needs value to be an array wherin each items needs type, value and name as keys.');

					// validate types
					if(!in_array($value['type'], $allowedTypes)) throw new SpoonXMLRPCException('Invalid parameter-type (' . $value['type'] . '). Possible types are: ' . implode(', ', $allowedTypes) . '.');
				}
			}
		}

		// build xml
		$xml = $this->buildXML($method, $parameters);

		// init curl options
		$options[CURLOPT_URL] = $this->getServer();
		$options[CURLOPT_PORT] = $this->getPort();
		$options[CURLOPT_USERAGENT] = $this->getUserAgent();
		$options[CURLOPT_TIMEOUT] = $this->getTimeout();
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_CUSTOMREQUEST] = 'POST';
		$options[CURLOPT_SSL_VERIFYPEER] = false;

		// get headers
		$headers = $this->getCustomHeaders();

		// set correct content type
		$headers[] = 'Content-type: text/xml';

		// set content-length
		$headers[] = 'Content-length: ' . strlen($xml) . "\r\n";

		// add XML
		$headers[] = $xml;

		// set headers
		$options[CURLOPT_HTTPHEADER] = $headers;

		// init curl
		$curl = curl_init();

		// set options
		curl_setopt_array($curl, $options);

		// execute
		$response = curl_exec($curl);
		$headers = curl_getinfo($curl);

		// fetch errors
		$errorNumber = curl_errno($curl);
		$errorMessage = curl_error($curl);

		// close curl
		curl_close($curl);

		// validate errors
		if($errorNumber != 0) throw new SpoonXMLRPCException('An error occured with the following message: (' . $errorNumber . ')' . $errorMessage . '.');

		// validate headers
		if($headers['http_code'] != 200) throw new SpoonXMLRPCException('Invalid headers, a header with status-code ' . $headers['http_code'] . ' was returned.');

		// we expect XML so decode it
		$xml = @simplexml_load_string($response, null, LIBXML_NOCDATA);

		// validate XML
		if($xml === false) throw new SpoonXMLRPCException('Invalid response.');

		// validate response, if it is an XMLRPC-error we'll throw it as an exception
		if($xml->getName() == 'fault')
		{
			// decode the fault
			$response = $this->decodeFaultXML($xml);

			// validate if the response was decoded, and it tye needed values are available
			if($response === false || !isset($response['faultString'])) throw new SpoonXMLRPCException('Unknown fault.');

			// everything is here
			else
			{
				// get faultcode
				$code = (isset($response['faultCode'])) ? (int) $response['faultCode'] : null;

				// get message
				$message = $response['faultString'];

				// throw exception
				throw new SpoonXMLRPCException($message, $code);
			}
		}

		// return the response
		return $this->decode($xml);
	}


	/**
	 * Get the headers.
	 *
	 * @return	array
	 */
	public function getCustomHeaders()
	{
		return $this->headers;
	}


	/**
	 * Get the port that will be used.
	 *
	 * @return	int
	 */
	public function getPort()
	{
		return $this->port;
	}


	/**
	 * Get the server that will be used.
	 *
	 * @return	string
	 */
	public function getServer()
	{
		return $this->server;
	}


	/**
	 * Get the timeout in seconds that will be used.
	 *
	 * @return	int
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}


	/**
	 * Get the user-agent that will be used. Keep in mind that a spoon header will be prepended.
	 *
	 * @return	string
	 */
	public function getUserAgent()
	{
		// prepend SpoonHeader
		$userAgent = 'Spoon ' . SPOON_VERSION . '/';
		$userAgent .= ($this->userAgent === null) ? 'SpoonXMLRPCClient' : $this->userAgent;

		// return
		return $userAgent;
	}


	/**
	 * Add custom headers that will be sent with each request.
	 *
	 * @param	array $headers		The header, passed as key-value pairs.
	 */
	public function setCustomHeader(array $headers)
	{
		foreach($headers as $name => $value) $this->headers[(string) $name] = (string) $value;
	}


	/**
	 * Set the port for the XMLRPC-server, default is 80.
	 *
	 * @param	int $port	The port to connect on.
	 */
	public function setPort($port)
	{
		$this->port = (int) $port;
	}


	/**
	 * Set the URL for the XMLRPC-server
	 *
	 * @return	vois
	 * @param	string $server	The server to connect to.
	 */
	public function setServer($server)
	{
		$this->server = (string) $server;
	}


	/**
	 * Set timeout.
	 *
	 * @param	int $seconds	The maximum number of seconds that the operation can last.
	 */
	public function setTimeout($seconds)
	{
		$this->timeout = (int) $seconds;
	}


	/**
	 * Set a custom user-agent.
	 *
	 * @param	string $userAgent	The UserAgent that will be used. It will look like "Spoon <Spoon version>/<your useragent>".
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = (string) $userAgent;
	}
}


/**
 * This exception is used to handle XMLRPC related exceptions.
 *
 * @package		spoon
 * @subpackage	xmlrpc
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.1.4
 */
class SpoonXMLRPCException extends SpoonException {}
