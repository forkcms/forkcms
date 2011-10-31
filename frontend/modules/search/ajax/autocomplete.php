<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the autocomplete-action, it will output a list of searches that start with a certain string.
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendSearchAjaxAutocomplete extends FrontendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$term = SpoonFilter::getPostValue('term', null, '');
		$limit = (int) FrontendModel::getModuleSetting('search', 'autocomplete_num_items', 10);

		// validate
		if($term == '') $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');

		// get matches
		$matches = FrontendSearchModel::getStartsWith($term, FRONTEND_LANGUAGE, $limit);

		// get search url
		$url = FrontendNavigation::getURLForBlock('search');

		// loop items and set search url
		foreach($matches as &$match) $match['url'] = $url . '?form=search&q=' . $match['term'];

		// output
		$this->output(self::OK, $matches);
	}
}
