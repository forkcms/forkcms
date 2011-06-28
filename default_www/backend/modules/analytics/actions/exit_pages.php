<?php

/**
 * This is the exit-pages-action, it will display the overview of analytics posts
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsExitPages extends BackendAnalyticsBase
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

		// parse exit pages
		$this->parseExitPages();

		// init google url
		$googleURL = BackendAnalyticsModel::GOOGLE_ANALYTICS_URL . '/%1$s?id=%2$s&amp;pdr=%3$s';
		$googleTableId = str_replace('ga:', '', BackendAnalyticsModel::getTableId());
		$googleDate = date('Ymd', $this->startTimestamp) . '-' . date('Ymd', $this->endTimestamp);

		// parse links to google
		$this->tpl->assign('googleTopExitPagesURL', sprintf($googleURL, 'exits', $googleTableId, $googleDate));
	}


	/**
	 * Parses the data to make the chart
	 *
	 * @return	void
	 */
	private function parseChartData()
	{
		// init vars
		$maxYAxis = 2;
		$metrics = array('exits');
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
	 * Parse exit pages datagrid
	 *
	 * @return	void
	 */
	private function parseExitPages()
	{
		// get results
		$results = BackendAnalyticsModel::getExitPages($this->startTimestamp, $this->endTimestamp);

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
			$dataGrid->setColumnURL('page', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');

			// parse the datagrid
			$this->tpl->assign('dgPages', $dataGrid->getContent());
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
			// exits percentage of total
			$exitsPercentageOfTotal = ($results['exits'] == 0) ? 0 : number_format(($results['exitPagesExits'] / $results['exits']) * 100, 0);

			// pageviews percentage of total
			$pageviewsPercentageOfTotal = ($results['pageviews'] == 0) ? 0 : number_format(($results['exitPagesPageviews'] / $results['pageviews']) * 100, 0);

			// exits percentage
			$exitsPercentage = ($results['exitPagesPageviews'] == 0) ? 0 : number_format(($results['exits'] / $results['exitPagesPageviews']) * 100, 0);
			$exitsPercentageTotal = ($resultsTotal['pageviews'] == 0) ? 0 : number_format(($resultsTotal['exits'] / $resultsTotal['pageviews']) * 100, 0);
			$exitsPercentageDifference = ($exitsPercentageTotal == 0) ? 0 : number_format((($exitsPercentage - $exitsPercentageTotal) / $exitsPercentageTotal) * 100, 0);
			if($exitsPercentageDifference > 0) $exitsPercentageDifference = '+' . $exitsPercentageDifference;

			// parse data
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

?>