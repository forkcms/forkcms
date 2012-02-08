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
	private $profiles, $yesterday, $allWeek;

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
		$this->profiles = BackendProfilesModel::getRegisteredToday();
		$this->yesterday = BackendProfilesModel::getRegisteredYesterday();
		$this->allWeek = BackendProfilesModel::getRegisteredAllWeek();
		$this->number = backendProfilesModel::getProfilesCount();
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		$this->tpl->assign('today', $this->profiles);
		$this->tpl->assign('yesterday', $this->yesterday);
		$this->tpl->assign('week', $this->allWeek);
		$this->tpl->assign('number', $this->number);
	}

	/**
	 * Parses the data to make the pie-chart
	 */
	private function parsePieChartData()
	{
		$graphData = array();
		
		$this->inactive = backendProfilesModel::getProfilesWithStatusCount('inactive');
		$this->active = backendProfilesModel::getProfilesWithStatusCount('active');
		$this->blocked = backendProfilesModel::getProfilesWithStatusCount('blocked');
		$this->deleted = backendProfilesModel::getProfilesWithStatusCount('deleted');
		$total = $this->inactive + $this->active + $this->deleted + $this->blocked;

		// build array
		$graphData[0]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase('NumberOfActiveProfiles', 'core')));
		$graphData[0]['value'] = $this->active;
		$graphData[0]['percentage'] = ($this->active / $total) * 100;
		
		$graphData[1]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase('NumberOfInactiveProfiles', 'core')));
		$graphData[1]['value'] = $this->inactive;
		$graphData[1]['percentage'] = ($this->inactive / $total) * 100;
		
		$graphData[2]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase('NumberOfBlockedProfiles', 'core')));
		$graphData[2]['value'] = $this->blocked;
		$graphData[2]['percentage'] = ($this->blocked / $total) * 100;
		
		$graphData[3]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase('NumberOfDeletedProfiles', 'core')));
		$graphData[3]['value'] = $this->deleted;
		$graphData[3]['percentage'] = ($this->deleted / $total) * 100;

		$this->tpl->assign('pieGraphData', $graphData);
	}
}
