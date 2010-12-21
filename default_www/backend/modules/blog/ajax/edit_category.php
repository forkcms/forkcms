<?php

/**
 * BackendBlogAjaxEditCategory
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendBlogAjaxEditCategory extends BackendBaseAJAXAction
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
		$id = SpoonFilter::getPostValue('id', null, 0, 'int');
		$categoryName = trim(SpoonFilter::getPostValue('value', null, '', 'string'));

		// validate
		if($id == 0) $this->output(self::BAD_REQUEST, null, 'no id provided');
		if($categoryName == '') $this->output(self::BAD_REQUEST, null, BL::getError('NameIsRequired'));

		// build array
		$category = array();
		$category['name'] = SpoonFilter::htmlspecialchars($categoryName);
		$category['language'] = BL::getWorkingLanguage();
		$category['url'] = BackendBlogModel::getURLForCategory($category['name']);

		// update
		BackendBlogModel::updateCategory($id, $category);

		// output
		$this->output(self::OK, $category, vsprintf(BL::getMessage('EditedCategory'), array($category['name'])));
	}
}

?>