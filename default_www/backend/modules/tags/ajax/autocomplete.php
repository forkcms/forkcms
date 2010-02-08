<?php

/**
 * BackendTagsAjaxAutocomplete
 *
 * This is the autocomplete-action, it will output a list of tags that start with a certain string.
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author 		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendTagsAjaxAutocomplete extends BackendBaseAJAXAction
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