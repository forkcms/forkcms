<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the content-action, it will display the overview of analytics posts
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsContent extends BackendAnalyticsBase
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
		$this->parseImportantPages();
		$this->parseImportantExitPages();
		$this->parseImportantLandingPages();

		$googleURL = BackendAnalyticsModel::GOOGLE_ANALYTICS_URL . '/%1$s?id=%2$s&amp;pdr=%3$s';
		$googleTableId = str_replace('ga:', '', BackendAnalyticsModel::getTableId());
		$googleDate = date('Ymd', $this->startTimestamp) . '-' . date('Ymd', $this->endTimestamp);

		// parse links to google
		$this->tpl->assign('googleTopContentURL', sprintf($googleURL, 'top_content', $googleTableId, $googleDate));
		$this->tpl->assign('googleTopExitPagesURL', sprintf($googleURL, 'exits', $googleTableId, $googleDate));
		$this->tpl->assign('googleTopLandingPagesURL', sprintf($googleURL, 'entrances', $googleTableId, $googleDate));
		$this->tpl->assign('googleContentURL', sprintf($googleURL, 'content', $googleTableId, $googleDate));
	}

	/**
	 * Parses the data to make the chart with
	 */
	private function parseChartData()
	{
		$maxYAxis = 2;
		$metrics = array('visitors', 'pageviews');
		$graphData = array();

		// get metrics per day
		$metricsPerDay = BackendAnalyticsModel::getMetricsPerDay($metrics, $this->startTimestamp, $this->endTimestamp);

		foreach($metrics as $i => $metric)
		{
			// build graph data array
			$graphData[$i] = array();
			$graphData[$i]['title'] = $metric;
			$graphData[$i]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($metric)));
			$graphData[$i]['i'] = $i + 1;
			$graphData[$i]['data'] = array();

			// loop metrics per day
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
	 * Parses the most important exit pages
	 */
	private function parseImportantExitPages()
	{
		$results = BackendAnalyticsModel::getTopExitPages($this->startTimestamp, $this->endTimestamp);
		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setColumnHidden('page_encoded');

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('detail_page', $this->getModule()))
			{
				$dataGrid->setColumnURL('page', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');
			}

			// parse the datagrid
			$this->tpl->assign('dgExitPages', $dataGrid->getContent());
		}
	}

	/**
	 * Parse the most important landing pages
	 */
	private function parseImportantLandingPages()
	{
		$results = BackendAnalyticsModel::getLandingPages($this->startTimestamp, $this->endTimestamp, 5);
		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setColumnsHidden('start_date', 'end_date', 'updated_on', 'page_encoded');

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('detail_page', $this->getModule()))
			{
				$dataGrid->setColumnURL('page_path', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');
			}

			// set headers
			$dataGrid->setHeaderLabels(
				array('page_path' => SpoonFilter::ucfirst(BL::lbl('Page')))
			);

			// parse the datagrid
			$this->tpl->assign('dgLandingPages', $dataGrid->getContent());
		}
	}

	/**
	 * Parses the most important pages
	 */
	private function parseImportantPages()
	{
		$results = BackendAnalyticsModel::getTopPages($this->startTimestamp, $this->endTimestamp);
		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setColumnHidden('page_encoded');

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('detail_page', $this->getModule()))
			{
				$dataGrid->setColumnURL('page', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');
			}

			// set headers
			$dataGrid->setHeaderLabels(
				array('pageviews_percentage' => '% ' . SpoonFilter::ucfirst(BL::lbl('Pageviews')))
			);

			// parse the datagrid
			$this->tpl->assign('dgContent', $dataGrid->getContent());
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
			// new visitors
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
			$this->tpl->assign('pageviewsTotal', $resultsTotal['pageviews']);
			$this->tpl->assign('uniquePageviews', $results['uniquePageviews']);
			$this->tpl->assign('uniquePageviewsTotal', $resultsTotal['uniquePageviews']);
			$this->tpl->assign('newVisits', $newVisits);
			$this->tpl->assign('newVisitsTotal', $newVisitsTotal);
			$this->tpl->assign('newVisitsDifference', $newVisitsDifference);
			$this->tpl->assign('bounces', $bounces);
			$this->tpl->assign('bouncesTotal', $bouncesTotal);
			$this->tpl->assign('bouncesDifference', $bouncesDifference);
		}
	}
}
