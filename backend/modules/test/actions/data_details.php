<?php

/**
 * This is the data-action it will display the details of a sent data item
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderDataDetails extends BackendBaseActionIndex
{
	/**
	 * Filter variables
	 *
	 * @var	array
	 */
	private $filter;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendFormBuilderModel::existsData($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// set filter
			$this->setFilter();

			// get data
			$this->getData();

			// parse
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// fetch data
		$this->data = BackendFormBuilderModel::getData($this->id);

		// fetch record
		$this->record = BackendFormBuilderModel::get($this->data['form_id']);
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// form info
		$this->tpl->assign('name', $this->record['name']);
		$this->tpl->assign('formId', $this->record['id']);

		// sent info
		$this->tpl->assign('id', $this->data['id']);
		$this->tpl->assign('sentOn', $this->data['sent_on']);

		// init
		$data = array();

		// prepare data
		foreach($this->data['fields'] as $field)
		{
			// implode arrays
			if(is_array($field['value'])) $field['value'] = implode(', ', $field['value']);

			// new lines to line breaks
			else $field['value'] = nl2br($field['value']);

			// add to data
			$data[] = $field;
		}

		// assign
		$this->tpl->assign('data', $data);
		$this->tpl->assign('filter', $this->filter);
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		// start date is set
		if(isset($_GET['start_date']) && $_GET['start_date'] != '')
		{
			// redefine
			$startDate = (string) $_GET['start_date'];

			// explode date parts
			$chunks = explode('/', $startDate);

			// valid date
			if(count($chunks) == 3 && checkdate((int) $chunks[1], (int) $chunks[0], (int) $chunks[2])) $this->filter['start_date'] = $startDate;

			// invalid date
			else $this->filter['start_date'] = '';
		}

		// not set
		else $this->filter['start_date'] = '';

		// end date is set
		if(isset($_GET['end_date']) && $_GET['end_date'] != '')
		{
			// redefine
			$endDate = (string) $_GET['end_date'];

			// explode date parts
			$chunks = explode('/', $endDate);

			// valid date
			if(count($chunks) == 3 && checkdate((int) $chunks[1], (int) $chunks[0], (int) $chunks[2])) $this->filter['end_date'] = $endDate;

			// invalid date
			else $this->filter['end_date'] = '';
		}

		// not set
		else $this->filter['end_date'] = '';
	}
}

?>