<?php

/**
 * This is the autocomplete-action, it will output a list of tags that start with a certain string.
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
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
		$term = SpoonFilter::getGetValue('term', null, '');

		// validate
		if($term == '') $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');

		// get tags
		$tags = BackendTagsModel::getStartsWith($term);

		// output
		$this->output(self::OK, $tags);
	}
}

?>