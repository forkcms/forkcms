<?php

/**
 * This widget will show the latest traffic sources
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsWidgetTrafficSources extends BackendBaseWidget
{
	/**
	 * Execute the widget
	 *
	 * @return	void
	 */
	public function execute()
	{
		// check analytics session token and analytics table id
		if(BackendModel::getModuleSetting('analytics', 'session_token', null) == '') return;
		if(BackendModel::getModuleSetting('analytics', 'table_id', null) == '') return;

		// settings are ok, set option
		$this->tpl->assign('analyticsValidSettings', true);

		// set column
		$this->setColumn('left');

		// set position
		$this->setPosition(0);

		// add highchart javascript
		$this->header->addJS('dashboard.js', 'analytics');

		// parse
		$this->parse();

		// get data
		$this->getData();

		// display
		$this->display();
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function getData()
	{
		// build url
		$URL = SITE_URL . '/backend/cronjob.php?module=analytics&action=get_traffic_sources&id=2';

		// set options
		$options = array();
		$options[CURLOPT_URL] = $URL;
		if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) $options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = 1;

		// init
		$curl = curl_init();

		// set options
		curl_setopt_array($curl, $options);

		// execute
		curl_exec($curl);

		// close
		curl_close($curl);
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse redirect link
		$this->tpl->assign('settingsUrl', BackendModel::createURLForAction('settings', 'analytics'));

		// parse keywords
		$this->parseKeywords();

		// parse referrers
		$this->parseReferrers();
	}


	/**
	 * Parse the keywords datagrid
	 *
	 * @return	void
	 */
	private function parseKeywords()
	{
		// get results
		$results = BackendAnalyticsModel::getRecentKeywords();

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// no pagination
			$dataGrid->setPaging(false);

			// hide columns
			$dataGrid->setColumnsHidden('id', 'date');

			// parse the datagrid
			$this->tpl->assign('dgAnalyticsKeywords', $dataGrid->getContent());
		}

		// get date
		$date = (isset($results[0]['date']) ? substr($results[0]['date'], 0, 10) : date('Y-m-d'));
		$timestamp = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));

		// assign date label
		$this->tpl->assign('analyticsTrafficSourcesDate', ($date != date('Y-m-d') ? BackendModel::getUTCDate('d-m', $timestamp) : BL::lbl('Today')));
	}


	/**
	 * Parse the referrers datagrid
	 *
	 * @return	void
	 */
	private function parseReferrers()
	{
		// get results
		$results = BackendAnalyticsModel::getRecentReferrers();

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// no pagination
			$dataGrid->setPaging(false);

			// hide columns
			$dataGrid->setColumnsHidden('id', 'date');

			// set url
			$dataGrid->setColumnURL('referrer', 'http://[referrer]');

			// parse the datagrid
			$this->tpl->assign('dgAnalyticsReferrers', $dataGrid->getContent());
		}
	}
}

?>