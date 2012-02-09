<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This widget will show the latest comments
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendProfilesWidgetRegisteredToday extends BackendBaseWidget
{
	/**
	 * The profiles
	 *
	 * @var array
	 */
	private $profiles, $graphData, $onlineNow, $barChart;

	/**
	 * Amount of profiles (& inactive, active, blocked, deleted)
	 * 
	 * @var number
	 */
	private $number, $inactive, $active, $blocked, $deleted;

	/**
	 * Datetime start and end
	 * 
	 * @var datetime
	 */
	private $start, $end;

	/**
	 * Execute the widget
	 */
	public function execute()
	{
		$this->header->addCSS('widgets.css', 'profiles');
		$this->header->addJS('highcharts.js', 'core');
		$this->header->addJS('registered_today.js', 'profiles');
		$this->setColumn('middle');
		$this->setPosition(0);
		$this->loadData();
		$this->loadBarChart();
		$this->loadPieChartData();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->start = new DateTime();
		$this->start->modify('-7 day');
		$this->end = new DateTime();

		$this->profiles = BackendProfilesModel::getRegisteredFromTo($this->start->format('Y-m-d'), $this->end->format('Y-m-d'));
		$this->number = BackendProfilesModel::getProfilesCount();
		$this->onlineNow = BackendProfilesModel::getOnlineUsers();
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		$this->tpl->assign('profiles', $this->profiles);
		$this->tpl->assign('number', $this->number);
		$this->tpl->assign('pieGraphData', $this->graphData);
		$this->tpl->assign('barGraphData', $this->barChart);
		$this->tpl->assign('online', $this->onlineNow);
	}

	/**
	 * loads the data to make the pie-chart
	 */
	private function loadPieChartData()
	{
		$this->inactive = BackendProfilesModel::getProfilesWithStatusCount('inactive');
		$this->active = BackendProfilesModel::getProfilesWithStatusCount('active');
		$this->blocked = BackendProfilesModel::getProfilesWithStatusCount('blocked');
		$this->deleted = BackendProfilesModel::getProfilesWithStatusCount('deleted');

		// build array
		$this->formatPieChartItem(0, 'NumberOfActiveProfiles', $this->active);
		$this->formatPieChartItem(1, 'NumberOfInactiveProfiles', $this->inactive);
		$this->formatPieChartItem(2, 'NumberOfBlockedProfiles', $this->blocked);
		$this->formatPieChartItem(3, 'NumberOfDeletedProfiles', $this->deleted);
	}

	/**
	 * loads the data for the barchart
	 * This is needed to create empty rows for dates without registration
	 */
	private function loadBarChart()
	{
		$this->barChart = BackendProfilesModel::getCountRegisteredPerDay($this->start->format('Y-m-d'), $this->end->format('Y-m-d'));
		$startDate = $this->start;
		$endDate = $this->end;
		while ($startDate <= $endDate) {
			$containsDate = false;
			foreach($this->barChart as $item)
			{
				if($item['date'] == $startDate->format('Y-m-d')) $containsDate = true;
			}
			if($containsDate == false)
			{
				$addArray = array('count' => 0, 'date' => $startDate->format('Y-m-d'));
				$this->barChart[] = $addArray;
			}
			$startDate->modify('+1 day');
		}

		function compare_date($a, $b)
		{
			return ($a['date']>$b['date'])?1:-1;
		}

		usort($this->barChart, 'compare_date');
	}

	/**
	 * adds one item to pieChartData
	 */
	private function formatPieChartItem($i, $label, $value)
	{
		$this->graphData[$i]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($label, 'core')));
		$this->graphData[$i]['value'] = $value;
		$this->graphData[$i]['percentage'] = ($value / $this->number) * 100;
	}
}
