<?php

/**
 * FrontendTagsDetail
 *
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendTagsDetail extends FrontendBaseBlock
{
	private $id;

	private $modules = array();


	/**
	 * The ?
	 *
	 * @var	array
	 */
	private $record = array();


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// load the data
		$this->getData();

		// parse
		$this->parse();
	}


	/**
	 * Load the data, don't forget to validate the incoming data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// fetch id
		$this->id = FrontendTagsModel::getIdByURL($this->URL->getParameter(1));

		// validate id
		if($this->id == 0) $this->redirect(FrontendNavigation::getURL(404));

		// fetch modules
		$this->modules = FrontendTagsModel::getModulesForTag($this->id);

		// loop modules
		foreach($this->modules as $module)
		{
			// check if this module actually is prepared to handle searches (well it should, because else there shouldn't be any search indices)
			if(method_exists('Frontend'. SpoonFilter::toCamelCase($module) .'Model', 'getForTags'))
			{
				// get the ids of the items linked to the tag
				$otherIds = (array) FrontendModel::getDB()->getColumn('SELECT other_id
																		FROM modules_tags
																		WHERE module = ? AND tag_id = ?;',
																		array($module, $this->id));

				// get the items that are linked to the tags
				$items = (array) call_user_func(array('Frontend'. SpoonFilter::toCamelCase($module) .'Model', 'getForTags'), $otherIds);

				// add into results array
				if(!empty($items)) $this->record[] = array('name' => $module, 'items' => $items);
			}
		}
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign tags
		$this->tpl->assign('tagsModules', $this->record);

		// update breadcrumb
		$this->breadcrumb->addElement(FrontendTagsModel::getName($this->id));
	}
}

?>