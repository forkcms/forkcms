<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the detail-page-action, it will display the overview of analytics posts
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsDetailPage extends BackendAnalyticsBase
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$this->pagePath = $this->getParameter('page', 'string');
		if($this->pagePath === null)
		{
			$this->redirect(BackendModel::createURLForAction('content'));
		}

		$this->parse();
		$this->display();
	}

	/**
	 * Parse this page
	 */
	protected function parse()
	{
		parent::parse();

		// get data
		$data = BackendAnalyticsModel::getDataForPage($this->pagePath, $this->startTimestamp, $this->endTimestamp);

		$this->parseOverviewData($data['aggregates']);
		$this->parseLineChartData($data['entries']['metrics_per_day']);

		// parse the page path
		$this->tpl->assign('pagePath', 'http://' . $data['entries']['hostname'] . $this->pagePath);

		$googleURL = BackendAnalyticsModel::GOOGLE_ANALYTICS_URL . '/%1$s?id=%2$s&amp;pdr=%3$s';
		$googleTableId = str_replace('ga:', '', BackendAnalyticsModel::getTableId());
		$googleDate = date('Ymd', $this->startTimestamp) . '-' . date('Ymd', $this->endTimestamp);

		// parse links to google
		$this->tpl->assign('googleContentDetailURL', sprintf($googleURL, 'content_detail', $googleTableId, $googleDate) . '&amp;d1=' . urlencode($this->pagePath));
	}

	/**
	 * Parses the data to make the line chart
	 *
	 * @param array $metricsPerDay All needed metrics grouped by day.
	 */
	private function parseLineChartData($metricsPerDay)
	{
		$maxYAxis = 2;
		$metrics = array('pageviews');
		$graphData = array();

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
		$this->tpl->assign('lineGraphData', $graphData);
	}

	/**
	 * Parses the overview data
	 *
	 * @param array $results The aggregates for the selected period.
	 */
	private function parseOverviewData($results)
	{
		// get total aggregates
		$resultsTotal = BackendAnalyticsModel::getAggregatesTotal($this->startTimestamp, $this->endTimestamp);

		// are there some values?
		$dataAvailable = false;
		foreach($resultsTotal as $data) if($data != 0) $dataAvailable = true;

		// show message if there is no data
		$this->tpl->assign('dataAvailable', $dataAvailable);

		if(!empty($results))
		{
			// time on page values
			$timeOnPage = ($results['pageviews'] - $results['exits'] == 0) ? 0 : ($results['timeOnPage'] / ($results['pageviews'] - $results['exits']));
			$timeOnPageTotal = ($results['pageviews'] - $results['exits'] == 0) ? 0 : ($resultsTotal['timeOnPage'] / ($resultsTotal['pageviews'] - $resultsTotal['exits']));
			$timeOnPageDifference = ($timeOnPageTotal == 0) ? 0 : number_format((($timeOnPage - $timeOnPageTotal) / $timeOnPageTotal) * 100, 0);
			if($timeOnPageDifference > 0) $timeOnPageDifference = '+' . $timeOnPageDifference;

			// pages / visit
			$pagesPerVisit = ($results['visits'] == 0) ? 0 : number_format(($results['pageviews'] / $results['visits']), 2);
			$pagesPerVisitTotal = ($resultsTotal['visits'] == 0) ? 0 : number_format(($resultsTotal['pageviews'] / $resultsTotal['visits']), 2);
			$pagesPerVisitDifference = ($pagesPerVisitTotal == 0) ? 0 : number_format((($pagesPerVisit - $pagesPerVisitTotal) / $pagesPerVisitTotal) * 100, 0);
			if($pagesPerVisitDifference > 0) $pagesPerVisitDifference = '+' . $pagesPerVisitDifference;

			// new visits
			$newVisits = ($results['entrances'] == 0) ? 0 : number_format(($results['newVisits'] / $results['entrances']) * 100, 0);
			$newVisitsTotal = ($resultsTotal['entrances'] == 0) ? 0 : number_format(($resultsTotal['newVisits'] / $resultsTotal['entrances']) * 100, 0);
			$newVisitsDifference = ($newVisitsTotal == 0) ? 0 : number_format((($newVisits - $newVisitsTotal) / $newVisitsTotal) * 100, 0);
			if($newVisitsDifference > 0) $newVisitsDifference = '+' . $newVisitsDifference;

			// bounces
			$bounces = ($results['entrances'] == 0) ? 0 : number_format(($results['bounces'] / $results['entrances']) * 100, 0);
			$bouncesTotal = ($resultsTotal['entrances'] == 0) ? 0 : number_format(($resultsTotal['bounces'] / $resultsTotal['entrances']) * 100, 0);
			$bouncesDifference = ($bouncesTotal == 0) ? 0 : number_format((($bounces - $bouncesTotal) / $bouncesTotal) * 100, 0);
			if($bouncesDifference > 0) $bouncesDifference = '+' . $bouncesDifference;

			$this->tpl->assign('pageviews', $results['pageviews']);
			$this->tpl->assign('visits', $results['visits']);
			$this->tpl->assign('pagesPerVisit', $pagesPerVisit);
			$this->tpl->assign('pagesPerVisitDifference', $pagesPerVisitDifference);
			$this->tpl->assign('timeOnPage', BackendAnalyticsModel::getTimeFromSeconds($timeOnPage));
			$this->tpl->assign('timeOnPageTotal', BackendAnalyticsModel::getTimeFromSeconds($timeOnPageTotal));
			$this->tpl->assign('timeOnPageDifference', $timeOnPageDifference);
			$this->tpl->assign('newVisits', $newVisits);
			$this->tpl->assign('newVisitsDifference', $newVisitsDifference);
			$this->tpl->assign('bounces', $bounces);
			$this->tpl->assign('bouncesDifference', $bouncesDifference);
		}
	}
}
