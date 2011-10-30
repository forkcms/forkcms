<?php

/**
 * This is the all-pages-action, it will display the overview of analytics posts
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsAllPages extends BackendAnalyticsBase
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

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Parse this page
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent parse
		parent::parse();

		// overview data
		$this->parseOverviewData();

		// get and parse data for chart
		$this->parseChartData();

		// parse pages
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
	 *
	 * @return	void
	 */
	private function parseChartData()
	{
		// init vars
		$maxYAxis = 2;
		$metrics = array('visitors', 'pageviews');
		$graphData = array();

		// get metrics per day
		$metricsPerDay = BackendAnalyticsModel::getMetricsPerDay($metrics, $this->startTimestamp, $this->endTimestamp);

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
			// loop the data
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
	 * Parses the overview data
	 *
	 * @return	void
	 */
	private function parseOverviewData()
	{
		// get aggregates
		$results = BackendAnalyticsModel::getAggregates($this->startTimestamp, $this->endTimestamp);

		// get total aggregates
		$resultsTotal = BackendAnalyticsModel::getAggregatesTotal($this->startTimestamp, $this->endTimestamp);

		// are there some values?
		$dataAvailable = false;
		foreach($resultsTotal as $data) if($data != 0) $dataAvailable = true;

		// show message if there is no data
		$this->tpl->assign('dataAvailable', $dataAvailable);

		// there are some results
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

			// parse data
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
	 *
	 * @return	void
	 */
	private function parsePages()
	{
		// get results
		$results = BackendAnalyticsModel::getPages($this->startTimestamp, $this->endTimestamp);

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// no pagination
			$dataGrid->setPaging();

			// hide columns
			$dataGrid->setColumnHidden('page_encoded');

			// set url
			$dataGrid->setColumnURL('page', BackendModel::createURLForAction('detail_page') . '&amp;page_path=[page_encoded]');

			// parse the datagrid
			$this->tpl->assign('dgPages', $dataGrid->getContent());
		}
	}
}

?>