<?php

/**
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class FrontendTagsDetail extends FrontendBaseBlock
{
	/**
	 * The tag
	 *
	 * @var	array
	 */
	private $record = array();


	/**
	 * The items per module with this tag
	 *
	 * @var array
	 */
	private $results = array();


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// hide contenTitle, in the template the title is wrapped with an inverse-option
		$this->tpl->assign('hideContentTitle', true);

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

		// fetch record
		$this->record = FrontendTagsModel::get($this->URL->getParameter(1));

		// validate record
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// fetch modules
		$this->modules = FrontendTagsModel::getModulesForTag($this->record['id']);

		// loop modules
		foreach($this->modules as $module)
		{
			// set module class
			$class = 'Frontend' . SpoonFilter::toCamelCase($module) . 'Model';

			// get the ids of the items linked to the tag
			$otherIds = (array) FrontendModel::getDB()->getColumn('SELECT other_id
																	FROM modules_tags
																	WHERE module = ? AND tag_id = ?',
																	array($module, $this->record['id']));

			// set module class
			$class = 'Frontend' . SpoonFilter::toCamelCase($module) . 'Model';

			// get the items that are linked to the tags
			$items = (array) FrontendTagsModel::callFromInterface($module, $class, 'getForTags', $otherIds);

			// add into results array
			if(!empty($items)) $this->results[] = array('name' => $module, 'label' => FL::lbl(ucfirst($module)), 'items' => $items);
		}
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign tag
		$this->tpl->assign('tag', $this->record);

		// assign tags
		$this->tpl->assign('tagsModules', $this->results);

		// update breadcrumb
		$this->breadcrumb->addElement($this->record['name']);
	}
}

?>