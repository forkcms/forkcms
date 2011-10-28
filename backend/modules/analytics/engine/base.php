<?php

/**
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsBase extends BackendBaseActionIndex
{
	/**
	 * The selected page
	 *
	 * @var	string
	 */
	protected $pagePath = null;


	/**
	 * The start and end timestamp of the collected data
	 *
	 * @var	int
	 */
	protected $startTimestamp, $endTimestamp;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// add highchart javascript
		$this->header->addJS('highcharts.js');

		// set dates
		$this->setDates();
	}


	/**
	 * Parse this page
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// periodpicker
		if(isset($this->pagePath)) BackendAnalyticsHelper::parsePeriodPicker($this->tpl, $this->startTimestamp, $this->endTimestamp, array('page_path' => $this->pagePath));
		else BackendAnalyticsHelper::parsePeriodPicker($this->tpl, $this->startTimestamp, $this->endTimestamp);
	}


	/**
	 * Set start and end timestamp needed to collect analytics data
	 *
	 * @return	void
	 */
	private function setDates()
	{
		// process
		BackendAnalyticsHelper::setDates();

		// get timestamps from session and set
		$this->startTimestamp = SpoonSession::get('analytics_start_timestamp');
		$this->endTimestamp = SpoonSession::get('analytics_end_timestamp');
	}
}

?>