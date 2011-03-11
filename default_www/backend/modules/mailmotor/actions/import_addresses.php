<?php

/**
 * BackendMailmotorImportAddresses
 * This is the import-action, it will import records from a CSV file
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorImportAddresses extends BackendBaseActionEdit
{
	/**
	 * The passed group ID
	 *
	 * @var	int
	 */
	private $groupId;


	/**
	 * Generates and downloads the example CSV file
	 *
	 * @return	void
	 */
	private function downloadExampleFile()
	{
		// Should we download the example file or not?
		$downloadExample = SpoonFilter::getGetValue('example', array(0, 1), 0, 'bool');

		// stop here if no download parameter was given
		if(!$downloadExample) return false;

		// build the csv
		$csv = array();
		$csv[] = array('email' => BackendModel::getModuleSetting('mailmotor', 'from_email'), 'name' => BackendModel::getModuleSetting('mailmotor', 'from_name'));

		// download the file
		SpoonFileCSV::arrayToFile(BACKEND_CACHE_PATH . '/mailmotor/example.csv', $csv, null, null, ',', '"', true);
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// store the passed group ID
		$this->groupId = SpoonFilter::getGetValue('group_id', null, 0, 'int');

		// download the example file
		$this->downloadExampleFile();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('import');

		// create elements
		$this->frm->addFile('csv');

		// dropdown for languages
		$this->frm->addDropdown('languages', BL::getWorkingLanguages(), BL::getWorkingLanguage());

		// fetch groups
		$groups = BackendMailmotorModel::getGroupsForCheckboxes();

		// if no groups are found, redirect to overview
		if(empty($groups)) $this->redirect(BackendModel::createURLForAction('addresses') . '&error=no-groups');

		// add radiobuttons for groups
		$this->frm->addRadiobutton('groups', $groups, (empty($this->groupId) ? null : $this->groupId));
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// shorten fields
			$fileCSV = $this->frm->getField('csv');
			$chkGroups = $this->frm->getField('groups');

			// validate fields
			$fileCSV->isFilled(BL::err('CSVIsRequired'));

			// convert the CSV file to an array
			$csv = SpoonFileCSV::fileToArray($fileCSV->getTempFileName());

			// check if the csv is valid
			if($csv === false || empty($csv) || !isset($csv[0])) $fileCSV->addError(BL::err('InvalidCSV'));

			// fetch the columns of the first row
			$columns = array_keys($csv[0]);

			// loop the columns
			foreach($csv as $row)
			{
				// fetch the row columns
				$rowColumns = array_keys($row);

				// check if the arrays match
				if($rowColumns != $columns)
				{
					// add an error to the CSV files
					$fileCSV->addError(BL::err('InvalidCSV'));

					// exit loop
					break;
				}
			}

			// get values
			$values = $this->frm->getValues();

			// check if at least one recipient group is chosen
			if(empty($values['groups'])) $chkGroups->addError(BL::err('ChooseAtLeastOneGroup'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// convert the CSV file to an array
				$csv = SpoonFileCSV::fileToArray($fileCSV->getTempFileName());

				// loop the addresses in the CSV and add+subscribe them
				foreach($csv as $record)
				{
					// no e-mail address set means stop here
					if(empty($record['email'])) continue;

					// build record to insert
					$item = array();
					$item['email'] = $record['email'];
					$item['source'] = BL::lbl('ImportNoun');
					$item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

					// unset the email (for the custom fields)
					unset($record['email']);

					// add/update a new subscriber
					BackendMailmotorCMHelper::subscribe($item['email'], $values['groups'], $record);

					// @later	Detailed reporting: downloadable .csv with all failed imports
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('addresses') . '&report=imported-addresses&var[]=' . count($csv) . '&var[]=' . count($values['groups']));
			}
		}
	}
}

?>
