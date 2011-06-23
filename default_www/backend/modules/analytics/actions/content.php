<?php

/**
 * This is the content-action, it will display the overview of analytics posts
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsContent extends BackendAnalyticsBase
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

		// get and parse overview data
		$this->parseOverviewData();

		// get and parse data for chart
		$this->parseChartData();

		// get and parse important pages
		$this->parseImportantPages();

		// get and parse important exit pages
		$this->parseImportantExitPages();

		// get and parse important entry pages
		$this->parseImportantLandingPages();

		// init google url
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
	 * Parses the most important exit pages
	 *
	 * @return	void
	 */
	private function parseImportantExitPages()
	{
		// get results
		$results = BackendAnalyticsModel::getTopExitPages($this->startTimestamp, $this->endTimestamp);

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// hide columns
			$dataGrid->setColumnHidden('page_encoded');

			// set url
			$dataGrid->setColumnURL('page', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');

			// parse the datagrid
			$this->tpl->assign('dgExitPages', $dataGrid->getContent());
		}
	}


	/**
	 * Parse the most important landing pages
	 *
	 * @return	void
	 */
	private function parseImportantLandingPages()
	{
		// get results
		$results = BackendAnalyticsModel::getLandingPages($this->startTimestamp, $this->endTimestamp, 5);

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// hide columns
			$dataGrid->setColumnsHidden('start_date', 'end_date', 'updated_on', 'page_encoded');

			// set headers values
			$headers['page_path'] = ucfirst(BL::lbl('Page'));

			// set headers
			$dataGrid->setHeaderLabels($headers);

			// set url
			$dataGrid->setColumnURL('page_path', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');

			// parse the datagrid
			$this->tpl->assign('dgLandingPages', $dataGrid->getContent());
		}
	}


	/**
	 * Parses the most important pages
	 *
	 * @return	void
	 */
	private function parseImportantPages()
	{
		// get results
		$results = BackendAnalyticsModel::getTopPages($this->startTimestamp, $this->endTimestamp);

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// hide columns
			$dataGrid->setColumnHidden('page_encoded');

			// set headers values
			$headers['pageviews_percentage'] = '% ' . ucfirst(BL::lbl('Pageviews'));

			// set headers
			$dataGrid->setHeaderLabels($headers);

			// set url
			$dataGrid->setColumnURL('page', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');

			// parse the datagrid
			$this->tpl->assign('dgContent', $dataGrid->getContent());
		}
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

			// parse data
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

?>