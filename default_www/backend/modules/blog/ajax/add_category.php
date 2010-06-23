<?php

/**
 * BackendBlogAjaxAddCategory
 *
 * @package		backend
 * @subpackage	blog
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

		$this->output(self::ERROR, null, 'ARF');

		// get parameters
		$categoryName = trim(SpoonFilter::getPostValue('category_name', null, '', 'string'));

		// validate
		if($categoryName == '') $this->output(self::BAD_REQUEST, null, 'no category_name provided');

		// get existing id
		$existingId = BackendBlogModel::getCategoryId($categoryName);

		// existing category
		if($existingId !== 0) $this->output(self::OK, array('id' => $existingId), 'category exists');

		// build array
		$category = array();
		$category['name'] = $categoryName;
		$category['language'] = BL::getWorkingLanguage();
		$category['url'] = BackendBlogModel::getURLForCategory($category['name']);

		// get page
		$id = BackendBlogModel::insertCategory($category);

		// output
		if($id !== 0) $this->output(self::OK, array('new_id' => $id), 'category added');
		else $this->output(self::ERROR, null, 'adding category failed');
	}
}

?>