<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the detail-action
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
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
	 */
	public function execute()
	{
		parent::execute();

		$this->tpl->assign('hideContentTitle', true);
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	/**
	 * Load the data, don't forget to validate the incoming data
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
			$otherIds = (array) FrontendModel::getDB()->getColumn(
				'SELECT other_id
				 FROM modules_tags
				 WHERE module = ? AND tag_id = ?',
				array($module, $this->record['id'])
			);

			// set module class
			$class = 'Frontend' . SpoonFilter::toCamelCase($module) . 'Model';

			// get the items that are linked to the tags
			$items = (array) FrontendTagsModel::callFromInterface($module, $class, 'getForTags', $otherIds);

			// add into results array
			if(!empty($items)) $this->results[] = array('name' => $module, 'label' => FL::lbl(SpoonFilter::ucfirst($module)), 'items' => $items);
		}
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// assign tag
		$this->tpl->assign('tag', $this->record);

		// assign tags
		$this->tpl->assign('tagsModules', $this->results);

		// update breadcrumb
		$this->breadcrumb->addElement($this->record['name']);

		// tag-pages don't have any SEO-value, so don't index them
		$this->header->addMetaData(array('name' => 'robots', 'content' => 'noindex, follow'), true);
	}
}
