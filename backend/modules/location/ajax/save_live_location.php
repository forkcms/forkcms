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
		$mapType = strtoupper(trim(SpoonFilter::getPostValue('type', array('roadmap', 'satelitte', 'hybrid', 'terrain'), 'roadmap')));
		$centerLat = SpoonFilter::getPostValue('centerLat', null, '', 'float');
		$centerlng = SpoonFilter::getPostValue('centerLng', null, '', 'float');
		$height = SpoonFilter::getPostValue('height', null, null, 'int');
		$width = SpoonFilter::getPostValue('width', null, null, 'int');

		$center = array('lat' => $centerLat, 'lng' => $centerlng);

		// no id given, this means we should update the main map
		BackendLocationModel::setMapSetting($itemId, 'zoom_level', (string) $zoomLevel);
		BackendLocationModel::setMapSetting($itemId, 'map_type', (string) $mapType);
		BackendLocationModel::setMapSetting($itemId, 'center', (array) $center);
		BackendLocationModel::setMapSetting($itemId, 'height', (int) $height);
		BackendLocationModel::setMapSetting($itemId, 'width', (int) $width);

		// output
		$this->output(self::OK, null, FL::msg('Success'));
	}
}
