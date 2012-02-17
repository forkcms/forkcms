<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
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
	 */
	public function execute()
	{
		parent::execute();
		$this->header->addJS('highcharts.js', 'core', false);
		$this->setDates();
	}

	/**
	 * Parse this page
	 */
	protected function parse()
	{
		// period picker
		if(isset($this->pagePath)) BackendAnalyticsHelper::parsePeriodPicker($this->tpl, $this->startTimestamp, $this->endTimestamp, array('page_path' => $this->pagePath));
		else BackendAnalyticsHelper::parsePeriodPicker($this->tpl, $this->startTimestamp, $this->endTimestamp);
	}

	/**
	 * Set start and end timestamp needed to collect analytics data
	 */
	private function setDates()
	{
		BackendAnalyticsHelper::setDates();

		$this->startTimestamp = SpoonSession::get('analytics_start_timestamp');
		$this->endTimestamp = SpoonSession::get('analytics_end_timestamp');
	}
}
