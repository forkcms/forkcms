<?php

/**
 * This widget will show the latest visitors
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsWidgetVisitors extends BackendBaseWidget
{
	/**
	 * Execute the widget
	 *
	 * @return	void
	 */
	public function execute()
	{
		// analytics session token and analytics table id
		if(BackendModel::getModuleSetting('analytics', 'session_token', null) == '') return;
		if(BackendModel::getModuleSetting('analytics', 'table_id', null) == '') return;

		// settings are ok, set option
		$this->tpl->assign('analyticsValidSettings', true);

		// set column
		$this->setColumn('right');

		// set position
		$this->setPosition(0);

		// add css
		$this->header->addCSS('widgets.css', 'analytics');

		// add highchart javascript
		$this->header->addJS('highcharts.js', 'analytics');
		$this->header->addJS('analytics.js', 'analytics');

		// parse
		$this->parse();

		// display
		$this->display();
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// init vars
		$maxYAxis = 2;
		$metrics = array('visitors', 'pageviews');
		$graphData = array();
		$startTimestamp = strtotime('-1 week -1 days', mktime(0, 0, 0));
		$endTimestamp = mktime(0, 0, 0);

		// get dashboard data
		$dashboardData = BackendAnalyticsModel::getDashboardData($metrics, $startTimestamp, $endTimestamp, true);

		// there are some metrics
		if($dashboardData !== false)
		{
			// loop metrics
			foreach($metrics as $i => $metric)
			{
				// build graph data array
				$graphData[$i] = array();
				$graphData[$i]['title'] = $metric;
				$graphData[$i]['label'] = ucfirst(BL::lbl(SpoonFilter::toCamelCase($metric)));
				$graphData[$i]['i'] = $i + 1;
				$graphData[$i]['data'] = array();

				// loop metrics per day
				foreach($dashboardData as $j => $data)
				{
					// cast SimpleXMLElement to array
					$data = (array) $data;

					// build array
					$graphData[$i]['data'][$j]['date'] = (int) $data['timestamp'];
					$graphData[$i]['data'][$j]['value'] = (string) $data[$metric];
				}
			}
		}

		// loop the metrics
		foreach($graphData as $metric)
		{
			// loop the data
			foreach($metric['data'] as $data)
			{
				// get the maximum value
				if((int) $data['value'] > $maxYAxis) $maxYAxis = (int) $data['value'];
			}
		}

		// parse
		$this->tpl->assign('analyticsRecentVisitsStartDate', $startTimestamp);
		$this->tpl->assign('analyticsRecentVisitsEndDate', $endTimestamp);
		$this->tpl->assign('analyticsMaxYAxis', $maxYAxis);
		$this->tpl->assign('analyticsMaxYAxis', $maxYAxis);
		$this->tpl->assign('analyticsTickInterval', ($maxYAxis == 2 ? '1' : ''));
		$this->tpl->assign('analyticsGraphData', $graphData);
	}
}

?>