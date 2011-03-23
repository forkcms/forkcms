<?php

/**
 * This edit-action will update a category using Ajax
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
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
		$categoryTitle = trim(SpoonFilter::getPostValue('value', null, '', 'string'));

		// validate
		if($id === 0) $this->output(self::BAD_REQUEST, null, 'no id provided');
		if($categoryTitle === '') $this->output(self::BAD_REQUEST, null, BL::err('TitleIsRequired'));

		// build array
		$item['id'] = $id;
		$item['title'] = SpoonFilter::htmlspecialchars($categoryTitle);
		$item['language'] = BL::getWorkingLanguage();

		$meta['keywords'] = $item['title'];
		$meta['keywords_overwrite'] = 'N';
		$meta['description'] = $item['title'];
		$meta['description_overwrite'] = 'N';
		$meta['title'] = $item['title'];
		$meta['title_overwrite'] = 'N';
		$meta['url'] = BackendBlogModel::getURLForCategory($item['title'], $id);

		// update
		BackendBlogModel::updateCategory($item, $meta);

		// output
		$this->output(self::OK, $item, vsprintf(BL::msg('EditedCategory'), array($item['title'])));
	}
}

?>