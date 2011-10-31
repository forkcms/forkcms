<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Export action, it will display a form to create a new item
 *
 * @author Jelmer Snoeck <jelmer@netlash.com>
 */
class BackendMailToFriendExport extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadData();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		$this->record = BackendMailToFriendModel::getAllForExport();
		$csvFile = SpoonFileCSV::arrayToString($this->record);

		// this is set so the browser will be forced to download the file
		SpoonHTTP::setHeaders(array(
			'Content-type: application/force-download',
			'Content-Disposition: inline; filename=mailtofriendexport.csv',
			'Content-Transfer-Encoding: Binary',
			'Content-length: ' . strlen($csvFile),
			'Content-Type: application/excel',
			'Content-Disposition: attachment; filename=mailtofriendexport.csv'
		));
		echo $csvFile;

		// exit so we don't leave the current page
		exit;
	}
}
