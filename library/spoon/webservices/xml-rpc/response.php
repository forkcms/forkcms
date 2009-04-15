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


/**
 * This base class provides all the methods used by a XML-RPC-response.
 *
 * @package			webservices
 * @subpackage		xml-rpc
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */
class SpoonXMLRPCResponse
{
	/**
	 * The body
	 *
	 * @var	string
	 */
	private $body;


	/**
	 * The error array
	 *
	 * @var	array
	 */
	private $error;


	/**
	 * The faultstatus
	 *
	 * @var unknown_type
	 */
	private $isError = false;


	/**
	 * The raw response
	 *
	 * @var	string
	 */
	private $response = '';


	/**
	 * The values
	 *
	 * @var	array
	 */
	private $value;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $response
	 */
	public function __construct($response)
	{
		if($response !== '')
		{
			// set properties
			$this->setResponse($response);

			// process response
			$this->processResponse();

			// process body
			$this->processBody();
		}
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
				return (isset($value[0])) ? $value[0] : '';

			case 'array':
				return $this->setValueArray($value['array']);

			case 'boolean':
				return (bool) ($value['boolean'] == 'true');

			case 'base64':
				return $value['base64'];

			case 'dateTime.iso8601':
				return (string) $value['dateTime.iso8601'];

			case 'double':
				return (double) $value['double'];

			case 'int':
				return (int) $value['int'];

			case 'string':
				return (string) $value['string'];

			case 'struct':
				return $this->setValueStruct($value['struct']);
			break;
		}
	}


	/**
	 * Gets the response body
	 *
	 * @return	string
	 */
	public function getBody()
	{
		return $this->body;
	}


	/**
	 * Gets the error-array
	 *
	 * @return	array
	 */
	public function getError()
	{
		return $this->error;
	}


	/**
	 * Gets the raw response
	 *
	 * @return	string
	 */
	public function getResponse()
	{
		return $this->response;
	}


	/**
	 * Get the values
	 *
	 * @return	array
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * is this a fault?
	 *
	 * @return	bool
	 */
	public function isError()
	{
		return $this->isError;
	}


	/**
	 * Processes the xml
	 *
	 * @return	void
	 */
	private function processBody()
	{
		$xml = @simplexml_load_string($this->body);
		if($xml === false)
		{
			if(SPOON_DEBUG) Spoon::dump($this->body, true, false);

			$this->setError(array('code' => 0, 'message' => 'invalid body'));
			return false;
		}

		// check if the response is a fault
		if(!isset($xml->fault) && isset($xml->params->param->value))
		{
			// get return value
			$value = (array) $xml->params->param->value;
			$key = array_keys($value);

			// validate
			if(!isset($key[0])) $this->setError(array('code' => '1', 'message' => 'Invalid response'));

			// set value
			else $this->setValue($this->decodeValue($key[0], $value));
		}
		else
		{
			if(isset($xml->fault->value->struct->member))
			{
				$members = $xml->fault->value->struct->member;

				// build array
				$aError = array();
				foreach ($members as $member)
				{
					if($member->name == 'faultCode') $aError['code'] = (int) $member->value->int;
					if($member->name == 'faultString') $aError['message'] = (string) $member->value->string;
				}

				// set error
				$this->setError($aError);
			}

			// invalid response
			else $this->setError(array('code' => '1', 'message' => 'Invalid response'));
		}
	}


	/**
	 * Processes the response
	 *
	 * @return	void
	 */
	private function processResponse()
	{
		$this->setBody($this->getResponse());
	}


	/**
	 * Set the body
	 *
	 * @return	void
	 * @param	string $body
	 */
	private function setBody($body)
	{
		$this->body = (string) $body;
	}


	/**
	 * Set an error
	 *
	 * @return	void
	 * @param	array $aError
	 */
	public function setError($aError)
	{
		// validate
		if(!isset($aError['code']) || !isset($aError['message'])) throw new SpoonXMLRPCException('This isn\'t a valid error-array.');

		// set properties
		$this->error = (array) $aError;
		$this->isError = true;
	}


	/**
	 * Sets the raw response
	 *
	 * @return	void
	 * @param	string $response
	 */
	private function setResponse($response)
	{
		$this->response = (string) $response;
	}


	/**
	 * Set value
	 *
	 * @param	mixed $value
	 */
	private function setValue($value)
	{
		$this->value = $value;
	}


	/**
	 * Set value
	 *
	 * @param	mixed $value
	 */
	private function setValueArray($xml)
	{
		// init var
		$array = array();

		// loop valies
		foreach ($xml->data->value as $value)
		{
			$value = (array) $value;
			$keys = array_keys($value);

			if(!isset($keys[0])) $keys[0] = '';

			$array[] = $this->decodeValue($keys[0], $value);
		}

		// return
		return $array;
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
			$key = (isset($member->name)) ? (string) $member->name : '';
			$value = (isset($member->value)) ? $member->value : null;

			if($value === null) $array[$key] = null;
			else
			{
				$value = (array) $value;
				$keys = array_keys($value);

				if(!isset($keys[0])) $keys[0] = '';

				$array[$key] = $this->decodeValue($keys[0], $value);
			}
		}
		return $array;
	}
}
?>