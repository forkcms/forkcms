<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the autocomplete-action, it will output a list of tags that start with a certain string.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendTagsAjaxAutocomplete extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$term = SpoonFilter::getPostValue('term', null, '');

		// validate
		if($term == '') $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');

		// get tags
		$tags = BackendTagsModel::getStartsWith($term);

		// output
		$this->output(self::OK, $tags);
	}
}
