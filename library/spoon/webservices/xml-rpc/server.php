<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			webservices
 * @subpackage		xml-rpc
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonXMLRPCException class */
require_once 'spoon/webservices/xml-rpc/exception.php';

/** SpoonXMLRPCResponse class */
require_once 'spoon/webservices/xml-rpc/method_response.php';

/** SpoonFilter class */
require_once 'spoon/filter/filter.php';

/** SPoonHTTP class */
require_once 'spoon/http/http.php';


/**
 * This base class provides all the methods used by a XML-RPC-client.
 *
 * @package			webservices
 * @subpackage		xml-rpc
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */
class SpoonXMLRPCServer
{
	/**
	 * Mapped functions
	 *
	 * @var	array
	 */
	private $functions = array();


	/**
	 * Function to call
	 *
	 * @var	string
	 */
	private $methodName;


	/**
	 * Parameters
	 *
	 * @var array
	 */
	private $parameters = array();


	/**
	 * Request body
	 *
	 * @var	string
	 */
	private $request = '';


	private $systemFunctions = array('system.listMethods', 'system.methodHelp');


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
	}


	/**
	 * Add a parameter
	 *
	 * @return	void
	 * @param	mixed $param
	 */
	private function addParameter($param)
	{
		$this->parameters[] = $param;
	}


	/**
	 * Decode a value
	 *
	 * @return	void
	 * @param	string $key
	 * @param	string $value
	 */
	private function decodeValue($key, $value)
	{
		switch($key)
		{
			case '':
				return $value[0];

			case 'array':
				return $this->setValueArray($value['array']);

			case 'boolean':
				return (bool) $value['boolean'];

			case 'base64':
				return $value['base64'];

			case 'dateTime.iso8601':
				return (string) $value['dateTime.iso8601'];

			case 'double':
				return (double) $value['double'];

			case 'int':
				return (int) $value['int'];

			case 'i4':
				return (int) $value['i4'];

			case 'string':
				return (string) $value['string'];

			case 'struct':
				return $this->setValueStruct($value['struct']);
			break;
		}
	}


	/**
	 * Get methodname
	 *
	 * @return	string
	 */
	public function getMethodName()
	{
		return (string) $this->methodName;
	}


	/**
	 * Get all parameters
	 *
	 * @return	array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * Get the request body
	 *
	 * @return	string
	 */
	public function getRequest()
	{
		return (string) $this->request;
	}


	/**
	 * Maps a XML-RPC to a existing function
	 *
	 * @return	void
	 * @param	string $methodName
	 * @param	mixed $mappedFunction
	 */
	public function mapFunction($methodName, $mappedFunction, $description = null)
	{
		if(!is_array($mappedFunction))
		{
			// function checks
			$mappedFunction = (string) $mappedFunction;
			if(!function_exists($mappedFunction)) throw new SpoonXMLRPCException('The function ('. $mappedFunction .') doesn\'t exist.');
		}

		// add mapped functions
		$this->functions[(string) $methodName]['function'] = $mappedFunction;
		$this->functions[(string) $methodName]['description'] = $description;
	}


	/**
	 * Process the request body
	 *
	 * @return	void
	 */
	private function processRequest()
	{
		// get xml
		$xml = simplexml_load_string($this->getRequest(), null, LIBXML_NOCDATA);
		if($xml === false) throw new SpoonXMLRPCException('invalid request');

		// get function name
		$this->setMethodName($xml->methodName);

		// count parameters
		$countParam = count($xml->params->param);

		// loop parameters
		for($i = 0; $i < $countParam; $i++)
		{
			$value = (array) $xml->params->param[$i]->value;
			$key = array_keys($value);

			if(empty($key)) $key[0] = '';
			if(empty($value)) $value[0] = null;

			// set value
			$this->addParameter($this->decodeValue($key[0], $value));
		}

		// validate
		if(!in_array($this->getMethodName(), $this->systemFunctions) && !isset($this->functions[$this->getMethodName()]['function']))
		{
			// create response
			$response = new SpoonXMLRPCMethodResponse();
			$response->setFault(1, 'unknow method');
			echo $response->buildXML();
			exit;
		}
	}


	/**
	 * Set the function name
	 *
	 * @return	void
	 * @param	string $string
	 */
	private function setMethodName($string)
	{
		$this->methodName = (string) $string;
	}


	/**
	 * Set the request body
	 *
	 * @return	void
	 * @param	string $string
	 */
	private function setRequest($string)
	{
		$this->request = (string) $string;
	}


	/**
	 * Set value
	 *
	 * @param	mixed $value
	 */
	private function setValueArray($xml)
	{
		foreach ($xml->data->value as $value)
		{
			$tmp = (array) $value;
			$keys = array_keys($tmp);
			$array[] = $this->decodeValue($keys[0], $tmp);
		}
		return (array) $array;
	}


	/**
	 * Set value
	 *
	 * @param	mixed $value
	 */
	private function setValueStruct($xml)
	{
		foreach ($xml->member as $member)
		{
			$key = (string) $member->name;
			$tmp = (array) $member->value;
			$keys = array_keys($tmp);
			$array[$key] = $this->decodeValue($keys[0], $tmp);
		}
		return (array) $array;
	}


	/**
	 * Start the server
	 *
	 * @return	void
	 */
	public function start()
	{
		// get raw data
		$request = @file_get_contents('php://input');
		if($request === false) throw new SpoonXMLRPCException('Can\'t read request.');

		// set properties
		$this->setRequest($request);

		// process response
		$this->processRequest();

		// create response
		$response = new SpoonXMLRPCMethodResponse();

		try
		{
			// get parameters
			$parameters = $this->getParameters();

			switch($this->getMethodName())
			{
				// list all methods
				case 'system.listMethods':
					$return = $this->systemListMethods();
				break;

				case 'system.methodHelp':
					$return = $this->systemMethodHelp($parameters[0]);
				break;

				default:
					// call function
					if(count($parameters) == 1) $return = call_user_func($this->functions[$this->getMethodName()]['function'], $parameters[0]);
					else $return = call_user_func_array($this->functions[$this->getMethodName()]['function'], $this->getParameters());
			}

			// add return parameter
			$type = gettype($return);

			// build parameter array
			switch($type)
			{
				case 'array':
					// get keys (we need to find out if this is a struct or a indexed aray
					$keys = array_keys($return);
					$numeric = true;
					foreach($keys as $key) if(!SpoonFilter::isDigital($key)) $numeric = false;

					// build parameter
					$parameter = ($numeric) ? array('type' => 'array', 'value' => $return) : array('type' => 'struct', 'value' => $return);
				break;

				case 'boolean':
					$parameter = array('type' => 'bool', 'value' => $return);
				break;

				case 'double':
					$parameter = array('type' => 'double', 'value' => $return);
				break;

				case 'int':
				case 'i4':
					$parameter = array('type' => 'int', 'value' => $return);
				break;

				case 'string':
					$parameter = array('type' => 'string', 'value' => $return);
				break;

				default:
					$parameter = array('type' => '', 'value' => $return);
			}

			// add parameter
			$response->addParameter($parameter);
		}
		catch (Exception $e)
		{
			$response->setFault(0, $e->getMessage());
		}

		// output response
		SpoonHTTP::setHeaders('Content-Type: text/xml');
		echo $response->buildXML();
		exit;
	}


	/**
	 * List all methods
	 *
	 * @return	array
	 */
	private function systemListMethods()
	{
		// init var
		$values = array();

		// loop functions
		foreach ($this->functions as $key => $function) $values[] = array('type' => 'string', 'value' => $key);
		foreach ($this->systemFunctions as $systemFunction) $values[] = array('type' => 'string', 'value' => $systemFunction);

		return $values;
	}


	/**
	 * Get more help about a method
	 *
	 * @return	string
	 * @param	string $methodName
	 */
	private function systemMethodHelp($methodName)
	{
		if(isset($this->functions[$methodName]['description'])) return $this->functions[$methodName]['description'];
		else return '';
	}
}

?>