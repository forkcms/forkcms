<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the ajax-action to update the registered users
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendProfilesAjaxGetRegistered extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 * 
	 * @return array
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$fromDate = SpoonFilter::getPostValue('from_date', null, '');
		$toDate = SpoonFilter::getPostValue('to_date', null, '');
		
		$profiles = BackendProfilesModel::getRegisteredFromTo($fromDate, $toDate);
		
		$this->output(self::OK, $profiles);
	}
}