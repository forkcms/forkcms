<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action, it has an overview of locations.
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class FrontendLocationIndex extends FrontendBaseBlock
{
	/**
	 * @var array
	 */
	protected $items = array(), $settings = array();

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();

		$this->loadTemplate();
		$this->loadData();

		$this->parse();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		$this->items = FrontendLocationModel::getAll();
		$this->settings = FrontendLocationModel::getMapSettings(0);
		$firstMarker = current($this->items);
		if(empty($this->settings))
		{
			$this->settings = FrontendModel::getModuleSettings('location');
			$this->settings['center']['lat'] = $firstMarker['lat'];
			$this->settings['center']['lng'] = $firstMarker['lng'];
		}

		// no center point given yet, use the first occurance
		if(!isset($this->settings['center']))
		{
			$this->settings['center']['lat'] = $firstMarker['lat'];
			$this->settings['center']['lng'] = $firstMarker['lng'];
		}
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		$this->tpl->assign('locationItems', $this->items);
		$this->tpl->assign('locationSettings', $this->settings);
	}
}
