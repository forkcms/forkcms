<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the exit-pages-action, it will display the overview of analytics posts
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsExitPages extends BackendAnalyticsBase
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->parse();
		$this->display();
	}

	/**
	 * Parse this page
	 */
	protected function parse()
	{
		parent::parse();
		$this->parseOverviewData();
		$this->parseChartData();
		$this->parseExitPages();

		$googleURL = BackendAnalyticsModel::GOOGLE_ANALYTICS_URL . '/%1$s?id=%2$s&amp;pdr=%3$s';
		$googleTableId = str_replace('ga:', '', BackendAnalyticsModel::getTableId());
		$googleDate = date('Ymd', $this->startTimestamp) . '-' . date('Ymd', $this->endTimestamp);

		// parse links to google
		$this->tpl->assign('googleTopExitPagesURL', sprintf($googleURL, 'exits', $googleTableId, $googleDate));
	}

	/**
	 * Parses the data to make the chart
	 */
	private function parseChartData()
	{
		$maxYAxis = 2;
		$metrics = array('exits');
		$graphData = array();

		// get metrics per day
		$metricsPerDay = BackendAnalyticsModel::getMetricsPerDay($metrics, $this->startTimestamp, $this->endTimestamp);

		foreach($metrics as $i => $metric)
		{
			// build graph data array
			$graphData[$i] = array();
			$graphData[$i]['title'] = $metric;
			$graphData[$i]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($metric)));
			$graphData[$i]['data'] = array();

			foreach($metricsPerDay as $j => $data)
			{
				// cast SimpleXMLElement to array
				$data = (array) $data;

				// build array
				$graphData[$i]['data'][$j]['date'] = (int) $data['timestamp'];
				$graphData[$i]['data'][$j]['value'] = (string) $data[$metric];
			}
		}

		// loop the metrics
		foreach($graphData as $metric)
		{
			foreach($metric['data'] as $data)
			{
				// get the maximum value
				if((int) $data['value'] > $maxYAxis) $maxYAxis = (int) $data['value'];
			}
		}

		// parse
		$this->tpl->assign('maxYAxis', $maxYAxis);
		$this->tpl->assign('tickInterval', ($maxYAxis == 2 ? '1' : ''));
		$this->tpl->assign('graphData', $graphData);
	}

	/**
	 * Parse exit pages datagrid
	 */
	private function parseExitPages()
	{
		$results = BackendAnalyticsModel::getExitPages($this->startTimestamp, $this->endTimestamp);
		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setPaging();
			$dataGrid->setColumnHidden('page_encoded');

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('detail_page', $this->getModule()))
			{
				$dataGrid->setColumnURL('page', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');
			}

			// parse the datagrid
			$this->tpl->assign('dgPages', $dataGrid->getContent());
		}
	}

	/**
	 * Parses the overview data
	 */
	private function parseOverviewData()
	{
		// get aggregates
		$results = BackendAnalyticsModel::getAggregates($this->startTimestamp, $this->endTimestamp);
		$resultsTotal = BackendAnalyticsModel::getAggregatesTotal($this->startTimestamp, $this->endTimestamp);

		// are there some values?
		$dataAvailable = false;
		foreach($resultsTotal as $data) if($data != 0) $dataAvailable = true;

		// show message if there is no data
		$this->tpl->assign('dataAvailable', $dataAvailable);

		if(!empty($results))
		{
			// exits percentage of total
			$exitsPercentageOfTotal = ($results['exits'] == 0) ? 0 : number_format(($results['exitPagesExits'] / $results['exits']) * 100, 0);

			// pageviews percentage of total
			$pageviewsPercentageOfTotal = ($results['pageviews'] == 0) ? 0 : number_format(($results['exitPagesPageviews'] / $results['pageviews']) * 100, 0);

			// exits percentage
			$exitsPercentage = ($results['exitPagesPageviews'] == 0) ? 0 : number_format(($results['exits'] / $results['exitPagesPageviews']) * 100, 0);
			$exitsPercentageTotal = ($resultsTotal['pageviews'] == 0) ? 0 : number_format(($resultsTotal['exits'] / $resultsTotal['pageviews']) * 100, 0);
			$exitsPercentageDifference = ($exitsPercentageTotal == 0) ? 0 : number_format((($exitsPercentage - $exitsPercentageTotal) / $exitsPercentageTotal) * 100, 0);
			if($exitsPercentageDifference > 0) $exitsPercentageDifference = '+' . $exitsPercentageDifference;

			$this->tpl->assign('exits', $results['exits']);
			$this->tpl->assign('exitsPercentageOfTotal', $exitsPercentageOfTotal);
			$this->tpl->assign('pageviews', $results['exitPagesPageviews']);
			$this->tpl->assign('pageviewsPercentageOfTotal', $pageviewsPercentageOfTotal);
			$this->tpl->assign('exitsPercentage', $exitsPercentage);
			$this->tpl->assign('exitsPercentageTotal', $exitsPercentageTotal);
			$this->tpl->assign('exitsPercentageDifference', $exitsPercentageDifference);
		}
	}
}
