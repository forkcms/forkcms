<?php

/**
 * This add-action will create a new category using Ajax
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBlogAjaxAddCategory extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$categoryTitle = trim(SpoonFilter::getPostValue('value', null, '', 'string'));

		// validate
		if($categoryTitle === '') $this->output(self::BAD_REQUEST, null, BL::err('TitleIsRequired'));

		// get the data
		// build array
		$item['title'] = SpoonFilter::htmlspecialchars($categoryTitle);
		$item['language'] = BL::getWorkingLanguage();

		$meta['keywords'] = $item['title'];
		$meta['keywords_overwrite'] = 'N';
		$meta['description'] = $item['title'];
		$meta['description_overwrite'] = 'N';
		$meta['title'] = $item['title'];
		$meta['title_overwrite'] = 'N';
		$meta['url'] = BackendBlogModel::getURLForCategory($item['title']);

		// update
		$item['id'] = BackendBlogModel::insertCategory($item, $meta);

		// output
		$this->output(self::OK, $item, vsprintf(BL::msg('AddedCategory'), array($item['title'])));
	}
}
