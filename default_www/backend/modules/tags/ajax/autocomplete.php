<?php

/**
 * TagsAdd
 *
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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

		if($query == '') $this->output('400', null, 'query is missing.');

		// get tags
		$tags = (array) BackendTagsModel::getStartsWith($query, $limit);

		// output
		$this->output(200, $tags);
	}
}

?>