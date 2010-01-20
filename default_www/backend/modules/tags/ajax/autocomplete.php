<?php

/**
 * TagsAdd
 *
 * This is the autocomplete-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class TagsAjaxAutocomplete extends BackendBaseAJAXAction
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
		$query = SpoonFilter::getGetValue('q', null, '');
		$limit = SpoonFilter::getGetValue('limit', null, 10);

		// validate
		if($query == '') $this->output(BackendBaseAJAXAction::BAD_REQUEST, null, 'Query is missing.');

		// get tags
		$tags = BackendTagsModel::getStartsWith($query, $limit);

		// output
		$this->output(BackendBaseAJAXAction::OK, $tags);
	}
}

?>