<?php

/**
 * This is the index-action (default), it will display the overview of analytics posts
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsIndex extends BackendAnalyticsBase
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

		// get warnings
		$warnings = BackendAnalyticsModel::checkSettings();

		// assign warnings
		$this->tpl->assign('warnings', $warnings);

		// no warnings
		if(empty($warnings))
		{
			// get and parse overview data
			$this->parseOverviewData();

			// get and parse data for chart
			$this->parseLineChartData();
			$this->parsePieChartData();

			// get and parse important referrals
			$this->parseImportantReferrals();

			// get and parse important keywords
			$this->parseImportantKeywords();

			// init google url
			$googleURL = BackendAnalyticsModel::GOOGLE_ANALYTICS_URL . '/%1$s?id=%2$s&amp;pdr=%3$s';
			$googleTableId = str_replace('ga:', '', BackendAnalyticsModel::getTableId());
			$googleDate = date('Ymd', $this->startTimestamp) . '-' . date('Ymd', $this->endTimestamp);

			// parse links to google
			$this->tpl->assign('googleTopReferrersURL', sprintf($googleURL, 'referring_sources', $googleTableId, $googleDate));
			$this->tpl->assign('googleTopKeywordsURL', sprintf($googleURL, 'keywords', $googleTableId, $googleDate));
			$this->tpl->assign('googleTopContentURL', sprintf($googleURL, 'top_content', $googleTableId, $googleDate));
			$this->tpl->assign('googleTrafficSourcesURL', sprintf($googleURL, 'sources', $googleTableId, $googleDate));
			$this->tpl->assign('googleVisitorsURL', sprintf($googleURL, 'visitors', $googleTableId, $googleDate));
			$this->tpl->assign('googlePageviewsURL', sprintf($googleURL, 'pageviews', $googleTableId, $googleDate));
			$this->tpl->assign('googleTimeOnSiteURL', sprintf($googleURL, 'time_on_site', $googleTableId, $googleDate));
			$this->tpl->assign('googleVisitorTypesURL', sprintf($googleURL, 'visitor_types', $googleTableId, $googleDate));
			$this->tpl->assign('googleBouncesURL', sprintf($googleURL, 'bounce_rate', $googleTableId, $googleDate));
			$this->tpl->assign('googleAveragePageviewsURL', sprintf($googleURL, 'average_pageviews', $googleTableId, $googleDate));
		}
	}


	/**
	 * Parses the most important keywords
	 *
	 * @return	void
	 */
	private function parseImportantKeywords()
	{
		// get results
		$results = BackendAnalyticsModel::getTopKeywords($this->startTimestamp, $this->endTimestamp, 25);

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// set headers values
			$headers['pageviews'] = ucfirst(BL::lbl('Views'));
			$headers['pageviews_percentage'] = '% ' . ucfirst(BL::lbl('Views'));

			// set headers
			$dataGrid->setHeaderLabels($headers);

			// parse the datagrid
			$this->tpl->assign('dgKeywords', $dataGrid->getContent());
		}
	}


	/**
	 * Parses the most important referrals
	 *
	 * @return	void
	 */
	private function parseImportantReferrals()
	{
		// get results
		$results = BackendAnalyticsModel::getTopReferrals($this->startTimestamp, $this->endTimestamp, 25);

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// hide columns
			$dataGrid->setColumnsHidden(array('referral_long'));

			// set headers values
			$headers['pageviews'] = ucfirst(BL::lbl('Views'));
			$headers['pageviews_percentage'] = '% ' . ucfirst(BL::lbl('Views'));

			// set column url
			$dataGrid->setColumnURL('referral', 'http://[referral_long]', '[referral_long]');

			// set headers
			$dataGrid->setHeaderLabels($headers);

			// parse the datagrid
			$this->tpl->assign('dgReferrers', $dataGrid->getContent());
		}
	}


	/**
	 * Parses the data to make the line-chart
	 *
	 * @return	void
	 */
	private function parseLineChartData()
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
			// time on site values
			$timeOnSite = ($results['entrances'] == 0) ? 0 : ($results['timeOnSite'] / $results['entrances']);
			$timeOnSiteTotal = ($resultsTotal['entrances'] == 0) ? 0 : ($resultsTotal['timeOnSite'] / $resultsTotal['entrances']);
			$timeOnSiteDifference = ($timeOnSiteTotal == 0) ? 0 : number_format((($timeOnSite - $timeOnSiteTotal) / $timeOnSiteTotal) * 100, 0);
			if($timeOnSiteDifference > 0) $timeOnSiteDifference = '+' . $timeOnSiteDifference;

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

			// parse data
			$this->tpl->assign('pageviews', $results['pageviews']);
			$this->tpl->assign('visitors', $results['visitors']);
			$this->tpl->assign('pageviews', $results['pageviews']);
			$this->tpl->assign('pageviewsTotal', $resultsTotal['pageviews']);
			$this->tpl->assign('pagesPerVisit', $pagesPerVisit);
			$this->tpl->assign('pagesPerVisitTotal', $pagesPerVisitTotal);
			$this->tpl->assign('pagesPerVisitDifference', $pagesPerVisitDifference);
			$this->tpl->assign('timeOnSite', BackendAnalyticsModel::getTimeFromSeconds($timeOnSite));
			$this->tpl->assign('timeOnSiteTotal', BackendAnalyticsModel::getTimeFromSeconds($timeOnSiteTotal));
			$this->tpl->assign('timeOnSiteDifference', $timeOnSiteDifference);
			$this->tpl->assign('newVisits', $newVisits);
			$this->tpl->assign('newVisitsTotal', $newVisitsTotal);
			$this->tpl->assign('newVisitsDifference', $newVisitsDifference);
			$this->tpl->assign('bounces', $bounces);
			$this->tpl->assign('bouncesTotal', $bouncesTotal);
			$this->tpl->assign('bouncesDifference', $bouncesDifference);
		}
	}


	/**
	 * Parses the data to make the pie-chart
	 *
	 * @return	void
	 */
	private function parsePieChartData()
	{
		// get sources
		$sources = BackendAnalyticsModel::getTrafficSourcesGrouped($this->startTimestamp, $this->endTimestamp);

		// init vars
		$graphData = array();

		// loop metrics
		foreach($sources as $i => $source)
		{
			// get label
			$label = BL::lbl(SpoonFilter::toCamelCase($source['label']), 'analytics');
			if($label == '{$lblAnalytics' . SpoonFilter::toCamelCase($source['label']) . '}') $label = $source['label'];

			// build array
			$graphData[$i]['label'] = ucfirst($label);
			$graphData[$i]['value'] = (string) $source['value'];
			$graphData[$i]['percentage'] = (string) $source['percentage'];
		}

		// parse
		$this->tpl->assign('pieGraphData', $graphData);
	}
}

?>