<?php

/**
 * This is the save-action, it will save the searched term in the statistics
 *
 * @package		frontend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class FrontendSearchAjaxSave extends FrontendBaseAJAXAction
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

		// previous search result
		$previousTerm = SpoonSession::exists('searchTerm') ? SpoonSession::get('searchTerm') : '';
		SpoonSession::set('searchTerm', '');

		// save this term?
		if($previousTerm != $term)
		{
			// format data
			$this->statistics = array();
			$this->statistics['term'] = $term;
			$this->statistics['language'] = FRONTEND_LANGUAGE;
			$this->statistics['time'] = FrontendModel::getUTCDate();
			$this->statistics['data'] = serialize(array('server' => $_SERVER));
			$this->statistics['num_results'] = FrontendSearchModel::getTotal($term);

			// save data
			FrontendSearchModel::save($this->statistics);
		}

		// save current search term in cookie
		SpoonSession::set('searchTerm', $term);

		// output
		$this->output(self::OK);
	}
}

?>