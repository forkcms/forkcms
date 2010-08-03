<?php

/**
 * FrontendSearchAjaxAutocomplete
 * This is the autocomplete-action, it will output a list of searches that start with a certain string.
 *
 * @package		frontend
 * @subpackage	search
 *
 * @author 		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class FrontendSearchAjaxAutocomplete extends FrontendBaseAJAXAction
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
		$language = SpoonFilter::getGetValue('language', FrontendLanguage::getActiveLanguages(), '');
		$limit = (int) SpoonFilter::getGetValue('limit', null, 50);

		// validate
		if($term == '') $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');

		// get matches
		$matches = FrontendSearchModel::getStartsWith($term, $language, $limit);

		// output
		$this->output(self::OK, $matches);
	}
}

?>