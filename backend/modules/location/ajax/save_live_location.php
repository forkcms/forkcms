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

		$generalSettings = BackendModel::getModuleSettings();
		$generalSettings = $generalSettings['location'];

		// get parameters
		$itemId = SpoonFilter::getPostValue('id', null, null, 'int');
		$zoomLevel = trim(SpoonFilter::getPostValue('zoom', null, 'auto'));
		$mapType = strtoupper(trim(SpoonFilter::getPostValue('type', array('roadmap', 'satelitte', 'hybrid', 'terrain'), 'roadmap')));
		$centerLat = SpoonFilter::getPostValue('centerLat', null, 1, 'float');
		$centerlng = SpoonFilter::getPostValue('centerLng', null, 1, 'float');
		$height = SpoonFilter::getPostValue('height', null, $generalSettings['height'], 'int');
		$width = SpoonFilter::getPostValue('width', null, $generalSettings['width'], 'int');
		$showLink = SpoonFilter::getPostValue('link', array('true', 'false'), 'false', 'string');
		$showDirections = SpoonFilter::getPostValue('directions', array('true', 'false'), 'false', 'string');
		$showOverview = SpoonFilter::getPostValue('showOverview', array('true', 'false'), 'true', 'string');

		// reformat
		$center = array('lat' => $centerLat, 'lng' => $centerlng);
		$showLink = ($showLink == 'true');
		$showDirections = ($showDirections == 'true');
		$showOverview = ($showOverview == 'true');

		// standard dimensions
		if($width > 800) $width = 800;
		if($width < 300) $width = $generalSettings['width'];
		if($height < 150) $height = $generalSettings['height'];

		// no id given, this means we should update the main map
		BackendLocationModel::setMapSetting($itemId, 'zoom_level', (string) $zoomLevel);
		BackendLocationModel::setMapSetting($itemId, 'map_type', (string) $mapType);
		BackendLocationModel::setMapSetting($itemId, 'center', (array) $center);
		BackendLocationModel::setMapSetting($itemId, 'height', (int) $height);
		BackendLocationModel::setMapSetting($itemId, 'width', (int) $width);
		BackendLocationModel::setMapSetting($itemId, 'directions', $showDirections);
		BackendLocationModel::setMapSetting($itemId, 'full_url', $showLink);

		$item = array(
			'id' => $itemId,
			'language' => BL::getWorkingLanguage(),
			'show_overview' => ($showOverview) ? 'Y' : 'N'
		);
		BackendLocationModel::update($item);

		// output
		$this->output(self::OK, null, FL::msg('Success'));
	}
}
