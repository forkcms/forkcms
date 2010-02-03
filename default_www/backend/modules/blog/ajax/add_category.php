<?php

/**
 * BackendPagesAjaxMove
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendBlogAjaxAddCategory extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$categoryName = trim(SpoonFilter::getPostValue('category_name', null, '', 'string'));

		// validate
		if($categoryName == '') $this->output(BackendBaseAJAXAction::BAD_REQUEST, null, 'no category_name provided');

		// get existing id
		$existingId = BackendBlogModel::getCategoryId($categoryName);

		// existing category
		if($existingId !== 0) $this->output(BackendBaseAJAXAction::OK, array('id' => $existingId), 'category exists');

		// build array
		$category = array();
		$category['name'] = $categoryName;
		$category['language'] = BL::getWorkingLanguage();
		$category['url'] = BackendBlogModel::getURLForCategory($category['name']);

		// get page
		$id = BackendBlogModel::insertCategory($category);

		// output
		if($id !== 0) $this->output(BackendBaseAJAXAction::OK, array('new_id' => $id), 'category added');
		else $this->output(BackendBaseAJAXAction::ERROR, null, 'adding category failed');
	}
}

?>