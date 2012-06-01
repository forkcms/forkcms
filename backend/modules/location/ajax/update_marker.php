<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is an ajax handler
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendLocationAjaxUpdateMarker extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$itemId = trim(SpoonFilter::getPostValue('id', null, '', 'int'));
		$lat = SpoonFilter::getPostValue('lat', null, null, 'float');
		$lng = SpoonFilter::getPostValue('lng', null, null, 'float');

		if($itemId == 0) $this->output(self::BAD_REQUEST, null, BL::err('NonExisting'));

		$updateData = array(
			'id' => $itemId,
			'lat' => $lat,
			'lng' => $lng,
			'language' => BL::getWorkingLanguage()
		);

		BackendLocationModel::update($updateData);

		// output
		$this->output(self::OK);
	}
}
