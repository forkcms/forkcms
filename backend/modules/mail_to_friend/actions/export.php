<?php

/**
 * This is the Export action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	mail_to_friend
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.10
 */
class BackendMailToFriendExport extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the data
		$this->loadData();
	}


	/**
	 * Load the data
	 *
	 * @return	void
	 */
	protected function loadData()
	{
		// get all the records
		$this->record = BackendMailToFriendModel::getAllForExport();

		// create the csv
		$csvFile = SpoonFileCSV::arrayToString($this->record);

		// set the headers
		SpoonHTTP::setHeaders(array(
			'Content-type: application/force-download',
			'Content-Disposition: inline; filename=mailtofriendexport.csv',
			'Content-Transfer-Encoding: Binary',
			'Content-length: ' . strlen($csvFile),
			'Content-Type: application/excel',
			'Content-Disposition: attachment; filename=mailtofriendexport.csv'
		));

		// print the file for download
		echo $csvFile;

		// exit so we don't leave the current page
		exit;
	}
}
