<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is an ajax handler that will set a new position for a certain map
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendLocationAjaxSaveLiveLocation extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$itemId = SpoonFilter::getPostValue('id', null, null, 'int');
		$zoomLevel = trim(SpoonFilter::getPostValue('zoom', null, ''));
		$mapType = trim(SpoonFilter::getPostValue('type', array('ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN'), 'ROADMAP'));
		$centerLat = SpoonFilter::getPostValue('centerLat', null, '', 'float');
		$centerlng = SpoonFilter::getPostValue('centerLng', null, '', 'float');

		$center = array('lat' => $centerLat, 'lng' => $centerlng);

		// no id given, this means we should update the main map
		if($itemId == 0)
		{
			BackendModel::setModuleSetting('location', 'zoom_level', (string) $zoomLevel);
			BackendModel::setModuleSetting('location', 'map_type', (string) $mapType);
			BackendModel::setModuleSetting('location', 'center', (array) $center);
		}

		// output
		$this->output(self::OK, null, FL::msg('Success'));
	}
}
