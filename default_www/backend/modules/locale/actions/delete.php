<?php

/**
 * This action will delete a translation
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Lowie Benoot <lowiebenoot@netlash.com>
 * @since		2.2
 */
class BackendLocaleDelete extends BackendBaseActionDelete
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
		if($this->id !== null && BackendLocaleModel::exists($this->id) && BackendAuthentication::getUser()->isGod())
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// filter options
			$this->setFilter();

			// get data
			$this->record = (array) BackendLocaleModel::get($this->id);

			// delete item
			BackendLocaleModel::delete(array($this->id));

			// build redirect URL
			$redirectUrl = BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['name'] . ' (' . strtoupper($this->record['language']) . ')') . $this->filterQuery;

			// item was deleted, so redirect
			$this->redirect($redirectUrl);
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		// get filter values
		$this->filter['language'] = ($this->getParameter('language', 'array') != '') ? $this->getParameter('language', 'array') : BL::getWorkingLanguage();
		$this->filter['application'] = $this->getParameter('application');
		$this->filter['module'] = $this->getParameter('module');
		$this->filter['type'] = $this->getParameter('type', 'array');
		$this->filter['name'] = $this->getParameter('name');
		$this->filter['value'] = $this->getParameter('value');

		// build query for filter
		$this->filterQuery = BackendLocaleModel::buildURLQueryByFilter($this->filter);
	}
}

?>