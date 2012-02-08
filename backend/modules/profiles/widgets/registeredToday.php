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
	private $profiles, $graphData;

	/**
	 * Amount of profiles (& inactive, active, blocked, deleted)
	 * 
	 * @var number
	 */
	private $number, $inactive, $active, $blocked, $deleted;

	/**
	 * Execute the widget
	 */
	public function execute()
	{
		$this->header->addCSS('widgets.css', 'profiles');
		$this->header->addJS('highcharts.js', 'core');
		$this->header->addJS('registeredToday.js', 'profiles');
		$this->setColumn('middle');
		$this->setPosition(0);
		$this->loadData();
		$this->parsePieChartData();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$start = new DateTime();
		$start->modify('-7 day');
		$end = new DateTime();

		$this->profiles = BackendProfilesModel::getRegisteredFromTo($start->format('Y-m-d'), $end->format('Y-m-d'));
		$this->number = backendProfilesModel::getProfilesCount();
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		$this->tpl->assign('profiles', $this->profiles);
		$this->tpl->assign('number', $this->number);
		$this->tpl->assign('pieGraphData', $this->graphData);
	}

	/**
	 * Parses the data to make the pie-chart
	 */
	private function parsePieChartData()
	{
		$this->inactive = backendProfilesModel::getProfilesWithStatusCount('inactive');
		$this->active = backendProfilesModel::getProfilesWithStatusCount('active');
		$this->blocked = backendProfilesModel::getProfilesWithStatusCount('blocked');
		$this->deleted = backendProfilesModel::getProfilesWithStatusCount('deleted');
		$total = $this->inactive + $this->active + $this->deleted + $this->blocked;

		// build array
		$this->parsePieChartItem(0, 'NumberOfActiveProfiles', $this->active, $total);
		$this->parsePieChartItem(1, 'NumberOfInactiveProfiles', $this->inactive, $total);
		$this->parsePieChartItem(2, 'NumberOfBlockedProfiles', $this->blocked, $total);
		$this->parsePieChartItem(3, 'NumberOfDeletedProfiles', $this->deleted, $total);
	}

	/**
	 * adds one item to pieChartData
	 */
	private function parsePieChartItem($i, $label, $value, $total)
	{
		$this->graphData[$i]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($label, 'core')));
		$this->graphData[$i]['value'] = $value;
		$this->graphData[$i]['percentage'] = ($value / $total) * 100;
	}
}
