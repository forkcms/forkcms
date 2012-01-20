<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the all-pages-action, it will display the overview of analytics posts
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsAllPages extends BackendAnalyticsBase
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
		$this->parsePages();

		// init google url
		$googleURL = BackendAnalyticsModel::GOOGLE_ANALYTICS_URL . '/%1$s?id=%2$s&amp;pdr=%3$s';
		$googleTableId = str_replace('ga:', '', BackendAnalyticsModel::getTableId());
		$googleDate = date('Ymd', $this->startTimestamp) . '-' . date('Ymd', $this->endTimestamp);

		// parse links to google
		$this->tpl->assign('googleTopContentURL', sprintf($googleURL, 'top_content', $googleTableId, $googleDate));
	}

	/**
	 * Parses the data to make the chart with
	 */
	private function parseChartData()
	{
		$maxYAxis = 2;
		$metrics = array('visitors', 'pageviews');
		$graphData = array();

		$metricsPerDay = BackendAnalyticsModel::getMetricsPerDay($metrics, $this->startTimestamp, $this->endTimestamp);

		foreach($metrics as $i => $metric)
		{
			$graphData[$i] = array();
			$graphData[$i]['title'] = $metric;
			$graphData[$i]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($metric)));
			$graphData[$i]['i'] = $i + 1;
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

		$this->tpl->assign('maxYAxis', $maxYAxis);
		$this->tpl->assign('tickInterval', ($maxYAxis == 2 ? '1' : ''));
		$this->tpl->assign('graphData', $graphData);
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
			// pageviews percentage of total
			$pageviewsPercentageOfTotal = ($results['pageviews'] == 0) ? 0 : number_format(($results['allPagesPageviews'] / $results['pageviews']) * 100, 0);

			// unique pageviews percentage of total
			$uniquePageviewsPercentageOfTotal = ($results['uniquePageviews'] == 0) ? 0 : number_format(($results['allPagesUniquePageviews'] / $results['uniquePageviews']) * 100, 0);

			// time on site values
			$timeOnSite = ($results['entrances'] == 0) ? 0 : ($results['timeOnSite'] / $results['entrances']);
			$timeOnSiteTotal = ($resultsTotal['entrances'] == 0) ? 0 : ($resultsTotal['timeOnSite'] / $resultsTotal['entrances']);
			$timeOnSiteDifference = ($timeOnSiteTotal == 0) ? 0 : number_format((($timeOnSite - $timeOnSiteTotal) / $timeOnSiteTotal) * 100, 0);
			if($timeOnSiteDifference > 0) $timeOnSiteDifference = '+' . $timeOnSiteDifference;

			// bounces
			$bounces = ($results['entrances'] == 0) ? 0 : number_format(($results['bounces'] / $results['entrances']) * 100, 0);
			$bouncesTotal = ($resultsTotal['entrances'] == 0) ? 0 : number_format(($resultsTotal['bounces'] / $resultsTotal['entrances']) * 100, 0);
			$bouncesDifference = ($bouncesTotal == 0) ? 0 : number_format((($bounces - $bouncesTotal) / $bouncesTotal) * 100, 0);
			if($bouncesDifference > 0) $bouncesDifference = '+' . $bouncesDifference;

			// exits percentage
			$exitsPercentage = ($results['allPagesPageviews'] == 0) ? 0 : number_format(($results['exits'] / $results['allPagesPageviews']) * 100, 0);
			$exitsPercentageTotal = ($resultsTotal['pageviews'] == 0) ? 0 : number_format(($resultsTotal['exits'] / $resultsTotal['pageviews']) * 100, 0);
			$exitsPercentageDifference = ($exitsPercentageTotal == 0) ? 0 : number_format((($exitsPercentage - $exitsPercentageTotal) / $exitsPercentageTotal) * 100, 0);
			if($exitsPercentageDifference > 0) $exitsPercentageDifference = '+' . $exitsPercentageDifference;

			$this->tpl->assign('timeOnSite', BackendAnalyticsModel::getTimeFromSeconds($timeOnSite));
			$this->tpl->assign('timeOnSiteTotal', BackendAnalyticsModel::getTimeFromSeconds($timeOnSiteTotal));
			$this->tpl->assign('timeOnSiteDifference', $timeOnSiteDifference);
			$this->tpl->assign('pageviews', $results['pageviews']);
			$this->tpl->assign('pageviewsPercentageOfTotal', $pageviewsPercentageOfTotal);
			$this->tpl->assign('uniquePageviews', $results['uniquePageviews']);
			$this->tpl->assign('uniquePageviewsPercentageOfTotal', $uniquePageviewsPercentageOfTotal);
			$this->tpl->assign('bounces', $bounces);
			$this->tpl->assign('bouncesTotal', $bouncesTotal);
			$this->tpl->assign('bouncesDifference', $bouncesDifference);
			$this->tpl->assign('exitsPercentage', $exitsPercentage);
			$this->tpl->assign('exitsPercentageTotal', $exitsPercentageTotal);
			$this->tpl->assign('exitsPercentageDifference', $exitsPercentageDifference);
		}
	}

	/**
	 * Parse pages datagrid
	 */
	private function parsePages()
	{
		$results = BackendAnalyticsModel::getPages($this->startTimestamp, $this->endTimestamp);
		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setPaging(false);
			$dataGrid->setColumnHidden('page_encoded');

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('detail_page', $this->getModule()))
			{
				$dataGrid->setColumnURL('page', BackendModel::createURLForAction('detail_page') . '&amp;page_path=[page_encoded]');
			}

			// parse the datagrid
			$this->tpl->assign('dgPages', $dataGrid->getContent());
		}
	}
}
