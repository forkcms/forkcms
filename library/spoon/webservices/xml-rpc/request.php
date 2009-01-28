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
 * This base class provides all the methods used by a XML-RPC-request.
 *
 * @package			webservices
 * @subpackage		xml-rpc
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */
class SpoonXMLRPCRequest
{
	private $aAllowedTypes = array('', 'array', 'base64', 'boolean', 'dateTime.iso8601', 'double', 'int', 'string', 'struct');

	/**
	 * The methodname
	 *
	 * @var	string
	 */
	private $methodName;


	/**
	 * The parameters
	 *
	 * @var	array
	 */
	private $parameters = array();


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $methodName
	 * @param	array $parameters
	 */
	public function __construct($methodName, $parameters = array())
	{
		// set properties
		$this->setMethodName($methodName);
		foreach ($parameters as $parameter) $this->addParameter($parameter);
	}


	/**
	 * Adds a parameter
	 *
	 * @return	void
	 * @param	array $parameter
	 */
	public function addParameter($parameter)
	{
		// validate parameter
		if(!isset($parameter['type']) && !isset($parameter['value'])) throw new SpoonXMLRPCException('This isn\'t a valid parameter, make sure the type- and valuekey exists.');
		if(!in_array($parameter['type'], $this->aAllowedTypes)) throw new SpoonXMLRPCException('This isn\'t a valid type ('. $parameter['type'] .').');

		// add parameter
		$this->parameters[] = (array) $parameter;
	}


	/**
	 * Builds xml for structs
	 *
	 * @return	string
	 * @param	array $array
	 * @param	string $xml
	 */
	private function buildArray($array, $xml = '')
	{
		// build xml
		$xml .= '<array>'."\n";
		$xml .= '<data>'."\n";

		// loop elements
		foreach ($array as $element) $xml .= $this->buildValue($element['type'], $element['value']);

		$xml .= '</data>'."\n";
		$xml .= '</array>'."\n";

		return $xml;
	}


	/**
	 * Builds xml for a value
	 *
	 * @return	string
	 * @param	string $type
	 * @param	mixed $value
	 */
	private function buildValue($type, $value)
	{
		switch ($type)
		{
			case 'array':
				return '<value>'. $this->buildArray($value) . '</value>'."\n";

			case 'base64':
				return '<value><base64>'. $value .'</base64></value>'."\n";

			case 'boolean':
				return '<value><boolean>'. $value .'</boolean></value>'."\n";

			case 'dateTime.iso8601':
				return '<value><dateTime.iso8601>'. $value .'</dateTime.iso8601></value>'."\n";

			case 'double':
				return '<value><double>'. $value .'</double></value>'."\n";

			case 'int':
				return '<value><int>'. $value .'</int></value>'."\n";

			case 'string':
				return '<value><string><![CDATA['. $value .']]></string></value>'."\n";

			case 'struct':
				// init struct
				$struct = '<value>'."\n";
				$struct .= '	<struct>'."\n";

				// loop array
				foreach ($value as $key => $value)
				{
					$struct .= str_replace("\n", '', '<member>'. "\n" .'<name>'. $key .'</name>'. self::buildValue($value['type'], $value['value']) .'</member>') . "\n";
				}

				$struct .= '	</struct>'."\n";
				$struct .= '</value>'."\n";

				// return
				return $struct;

			default:
				return '<value>'. $value .'</value>'."\n";
		}
	}


	/**
	 * Builds xml for struct
	 *
	 * @return	string
	 * @param	array $array
	 * @param	string $xml
	 */
	private function buildStruct($array, $xml = '')
	{
		// build xml
		$xml .= '<struct>'."\n";

		// loop elements
		foreach ($array as $element) $xml .= $this->buildValue($element['type'], $element['value']);

		$xml .= '</struct>'."\n";

		return $xml;
	}


	/**
	 * Build the xml-request
	 *
	 * @return	string
	 */
	public function buildXML()
	{
		// create xml
		$xml = '<?xml version="1.0"?>'."\n";
		$xml .= '<methodCall>'."\n";

		// set methodname
		$xml .= '<methodName>'. $this->getMethodName() .'</methodName>'."\n";

		// set parameters
		$xml .= '<params>'."\n";

		// loop parameters
		foreach ($this->getParameters() as $parameter)
		{
			$xml .= '<param>'."\n";
			$xml .= $this->buildValue($parameter['type'], $parameter['value']);
			$xml .= '</param>'."\n";
		}
		$xml .= '</params>'."\n";
		$xml .= '</methodCall>'."\n";

		// return
		return $xml;
	}


	/**
	 * Gets the methodname
	 *
	 * @return	string
	 */
	public function getMethodName()
	{
		return $this->methodName;
	}


	/**
	 * Gets the parameterlist
	 *
	 * @return	array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * Sets the methodname
	 *
	 * @return	void
	 * @param	string $methodName
	 */
	public function setMethodName($methodName)
	{
		$this->methodName = (string) $methodName;
	}
}

?>