<?php

// @todo davy - volledig herwerken

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
	private $id; // @todo moet deze een property zijn?

	private $modules = array(); // @todo moet deze een property zijn?


	/**
	 * The blogpost
	 *
	 * @var	array
	 */
	private $record = array(); // @todo is deze nodig ?


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

		// item doesn't exist
		if(!FrontendTagsModel::exists($this->URL->getParameter(1))) $this->redirect(FrontendNavigation::getURL(404));

		// fetch id
		$this->id = FrontendTagsModel::getIdByURL($this->URL->getParameter(1));

		// fetch modules
		$this->modules = FrontendTagsModel::getModulesForTag($this->id);// @todo steekt url hier ineens in.

		require_once FRONTEND_PATH .'/modules/blog/engine/model.php';

		// loop modules
		foreach($this->modules as $module)
		{
			if($module == 'blog')
			{
				$otherIds = (array) FrontendModel::getDB()->getColumn('SELECT other_id FROM modules_tags WHERE module = ? AND tag_id = ?;', array('blog', $this->id));

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