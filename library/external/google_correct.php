<?php

/**
 * This source file can be used to comunicate with Google corrections.
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to jelmer.snoeck@netlash.com
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * @author	Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class GoogleCorrect
{
	/**
	 * The url to call to
	 *
	 * @var	string
	 */
	const SUGGEST_URL = 'https://www.google.com';

	/**
	 * The call path
	 *
	 * @var	string
	 */
	private $callPath;

	/**
	 * The language
	 *
	 * @var	string
	 */
	private $language;

	/**
	 * The search string
	 *
	 * @var	string
	 */
	private $searchTerm;

	/**
	 * The response xml
	 *
	 * @var array
	 */
	private $responseData;

	/**
	 * The constructor
	 *
	 * @param	string $language		The language to search in.
	 * @param	string[optional] $term	The term to search;
	 */
	public function __construct($language, $term = null)
	{
		// set the language
		$this->language = $language;

		// build the url string
		$this->buildUrlString();

		// if a term is given, set it
		if($term !== null) $this->setSearchTerm($term);
	}

	/**
	 * Builds the url string
	 */
	private function buildUrlString()
	{
		$this->callPath = '/tbproxy/spell?lang=nl&hl=en';
	}

	/**
	 * Set the search term
	 *
	 * @param	string $term		The search term.
	 */
	public function setSearchTerm($term)
	{
		$this->searchTerm = (string) $term;
	}

	/**
	 * Fetches the search term
	 *
	 * @return	string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * Do the call to google
	 */
	public function doCall()
	{
		// no search term set
		if($this->searchTerm == null) return;

		// setup XML request
		$xml = '<?xml version="1.0" encoding="utf-8" ?>';
		$xml.= '<spellrequest textalreadyclipped="0" ignoredups="0" ignoredigits="1" ignoreallcaps="1">';
		$xml.= '<text>' . $this->getSearchTerm() . '</text></spellrequest>';

		// setup headers to be sent
		$cHeader = "POST {$this->callPath} HTTP/1.0 \r\n";
		$cHeader.= "MIME-Version: 1.0 \r\n";
		$cHeader.= "Content-type: text/xml; charset=utf-8 \r\n";
		$cHeader.= "Content-length: " . strlen($xml) . " \r\n";
		$cHeader.= "Request-number: 1 \r\n";
		$cHeader.= "Document-type: Request \r\n";
		$cHeader.= "Connection: close \r\n\r\n";
		$cHeader.= $xml;

		// the curl options
		$cOptions[CURLOPT_URL] = self::SUGGEST_URL;
		$cOptions[CURLOPT_RETURNTRANSFER] = 1;
		$cOptions[CURLOPT_CUSTOMREQUEST] = $cHeader;
		$cOptions[CURLOPT_SSL_VERIFYPEER] = false;

		// start the curl request
		$curl = curl_init();

		curl_setopt_array($curl, $cOptions);

		// execution
		$response = curl_exec($curl);

		// set the response
		$this->setResponseData($response);

		// close the curl cal
		curl_close($curl);
	}

	/**
	 * This converts the response data into an array with all the corrections
	 *
	 * @param	string $response		The xml response in string format.
	 */
	private function setResponseData($response)
	{
		// build the xml element
		$xmlElement = new SimpleXMLElement($response);

		// the new data
		$strData = '';
		$newData = array();

		// get the corrections
		$corrections = $xmlElement->spellresult;

		// convert the data
		foreach($xmlElement->children() as $child) if(isset($child[0])) $strData.= (string) $child[0];

		// split the data
		$newData = explode('	', $strData);

		// save the data
		$this->responseData = $newData;
	}

	/**
	 * Fetches the response
	 *
	 * @return	array
	 */
	public function getResponse()
	{
		return $this->responseData;
	}
}
