<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the export-action, it will create a XML with missing locale items.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
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
	 */
	private function createXML()
	{
		// create XML
		$xmlOutput = BackendLocaleModel::createXMLForExport($this->locale);

		// xml headers
		$headers[] = 'Content-Disposition: attachment; filename="locale_' . BackendModel::getUTCDate('d-m-Y') . '.xml"';
		$headers[] = 'Content-Type: application/octet-stream;charset=' . SPOON_CHARSET;
		$headers[] = 'Content-Length: ' . strlen($xmlOutput);

		// set headers
		SpoonHTTP::setHeaders($headers);

		// output XML
		echo $xmlOutput;
		exit;
	}

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();
		$this->setFilter();
		$this->setItems();
		$this->createXML();
	}

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
		$this->filter['language'] = (isset($_GET['language'])) ? $this->getParameter('language') : BL::getWorkingLanguage();
	}

	/**
	 * Build items array and group all items by application, module, type and name.
	 */
	private function setItems()
	{
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
