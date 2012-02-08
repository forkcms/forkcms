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
	 * Execute the widget
	 */
	public function execute()
	{
		$this->header->addCSS('widgets.css', 'profiles');
		$this->setColumn('middle');
		$this->setPosition(0);
		$this->loadData();
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
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		$this->tpl->assign('today', $this->profiles);
		$this->tpl->assign('yesterday', $this->yesterday);
		$this->tpl->assign('week', $this->allWeek);
	}
}
