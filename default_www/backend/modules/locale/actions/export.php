<?php

/**
 * This is the export-action, it will create a XML with locale items.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @author		Lowie Benoot <lowie@netlash.com>
 * @since		2.0
 */
class BackendLocaleExport extends BackendBaseActionIndex
{
	/**
	 * Filter variables.
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
	 * Builds the query for this datagrid.
	 *
	 * @return	array		An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		// init var
		$parameters = array();

		// start of query
		$query = 'SELECT l.id, l.language, l.application, l.module, l.type, l.name, l.value
					FROM locale AS l
					WHERE 1';

		// add language
		if($this->filter['language'] !== null)
		{
			// create an array for the languages, surrounded by quotes (example: 'nl')
			$languages = array();
			foreach($this->filter['language'] as $key => $val) $languages[$key] = '\'' . $val . '\'';

			$query .= ' AND l.language IN (' . implode(',', $languages) . ')';
		}

		// add application
		if($this->filter['application'] !== null)
		{
			$query .= ' AND l.application = ?';
			$parameters[] = $this->filter['application'];
		}

		// add module
		if($this->filter['module'] !== null)
		{
			$query .= ' AND l.module = ?';
			$parameters[] = $this->filter['module'];
		}

		// add type
		if($this->filter['type'] !== null)
		{
			// create an array for the types, surrounded by quotes (example: 'lbl')
			$types = array();
			foreach($this->filter['type'] as $key => $val) $types[$key] = '\'' . $val . '\'';

			$query .= ' AND l.type IN (' . implode(',', $types) . ')';
		}

		// add name
		if($this->filter['name'] !== null)
		{
			$query .= ' AND l.name LIKE ?';
			$parameters[] = '%' . $this->filter['name'] . '%';
		}

		// add value
		if($this->filter['value'] !== null)
		{
			$query .= ' AND l.value LIKE ?';
			$parameters[] = '%' . $this->filter['value'] . '%';
		}

		// end of query
		$query .= ' ORDER BY l.application, l.module, l.name ASC';

		// cough up
		return array($query, $parameters);
	}


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
		$this->filter['application'] = $this->getParameter('application') == null ? 'backend' : $this->getParameter('application');
		$this->filter['module'] = $this->getParameter('module');
		$this->filter['type'] = $this->getParameter('type', 'array');
		$this->filter['language'] = $this->getParameter('language', 'array');
		$this->filter['name'] = $this->getParameter('name') == null ? '' : $this->getParameter('name');
		$this->filter['value'] = $this->getParameter('value') == null ? '' : $this->getParameter('value');
	}


	/**
	 * Build items array and group all items by application, module, type and name.
	 *
	 * @return	void
	 */
	private function setItems()
	{
		// build our query
		list($query, $parameters) = $this->buildQuery();

		// get locale from the database
		$items = (array) BackendModel::getDB()->getRecords($query, $parameters);

		// init
		$this->locale = array();

		// group by application, module, type and name
		foreach($items as $item)
		{
			$this->locale[$item['application']][$item['module']][$item['type']][$item['name']][] = $item;
		}

		// no need to keep this around
		unset($items);
	}
}

?>