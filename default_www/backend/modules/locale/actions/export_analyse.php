<?php

/**
 * This is the export-action, it will create a XML with missing locale items.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendLocaleExportAnalyse extends BackendBaseActionIndex
{
	/**
	 *
	 * @var	array
	 */
	private $filter;


	/**
	 * Locale items.
	 *
	 * @var	array
	 */
	private $locale;


	/**
	 * Create the XML based on the locale items.
	 *
	 * @return	void
	 */
	private function createXML()
	{
		// create XML
		$xmlOutput = BackendLocaleModel::createXMLForExport($this->locale);

		// xml headers
		$headers[] = 'Content-Disposition: attachment; filename="locale_' . BackendModel::getUTCDate('d-m-Y') . '.xml"';
		$headers[] = 'Content-Type: application/octet-stream;charset=utf-8';
		$headers[] = 'Content-Length: ' . strlen($xmlOutput);

		// set headers
		SpoonHTTP::setHeaders($headers);

		// output XML
		echo $xmlOutput;

		// stop script
		exit;
	}


	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// set filter
		$this->setFilter();

		// set items
		$this->setItems();

		// create XML
		$this->createXML();
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		$this->filter['language'] = (isset($_GET['language'])) ? $this->getParameter('language') : BL::getWorkingLanguage();
	}


	/**
	 * Build items array and group all items by application, module, type and name.
	 *
	 * @return	void
	 */
	private function setItems()
	{
		// init
		$this->locale = array();

		// get items
		$frontend = BackendLocaleModel::getNonExistingFrontendLocale($this->filter['language']);

		// group by application, module, type and name
		foreach($frontend as $item)
		{
			$item['value'] = null;

			$this->locale[$item['application']][$item['module']][$item['type']][$item['name']][] = $item;
		}

		// no need to keep this around
		unset($frontend);

		// get items
		$backend = BackendLocaleModel::getNonExistingBackendLocale($this->filter['language']);

		// group by application, module, type and name
		foreach($backend as $item)
		{
			$item['value'] = null;

			$this->locale[$item['application']][$item['module']][$item['type']][$item['name']][] = $item;
		}

		// no need to keep this around
		unset($backend);
	}
}

?>