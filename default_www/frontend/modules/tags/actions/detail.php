<?php

// @later davy - code herwerken

/**
 * FrontendTagsDetail
 *
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author 		Davy Hellemans <davy@netlash.com>
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

		require_once FRONTEND_PATH .'/modules/blog/engine/model.php';

		// loop modules
		foreach($this->modules as $module)
		{
			if($module == 'blog')
			{
				$otherIds = (array) FrontendModel::getDB()->getColumn('SELECT other_id
																		FROM modules_tags
																		WHERE module = ? AND tag_id = ?;',
																		array('blog', $this->id));

				$this->record[] = array('name' => $module, 'items' => FrontendBlogModel::getForTags($otherIds));

			}

			elseif($module == 'pages')
			{

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
		$this->tpl->assign('tagsModules', $this->record);
	}
}

?>