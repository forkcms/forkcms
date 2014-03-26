<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the import-action, it will display a form to do an import of multiple profiles.
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class BackendProfilesImport extends BackendBaseActionAdd
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// get group values for dropdown
		$ddmValues = BackendProfilesModel::getGroupsForDropDown($this->id);

		// create form
		$this->frm = new BackendForm('import');

		// create elements
		$this->frm->addDropdown('group', $ddmValues);
		$this->frm->addFile('file');
		$this->frm->addCheckbox('overwrite_existing');
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// get fields
			$ddmGroup = $this->frm->getField('group');
			$fileFile = $this->frm->getField('file');

			// fields filled?
			$ddmGroup->isFilled(BL::getError('FieldIsRequired'));

			// name checks
			if($fileFile->isFilled(BL::err('FieldIsRequired')))
			{
				// only xml files allowed
				if($fileFile->isAllowedExtension(array('csv'), sprintf(BL::getError('ExtensionNotAllowed'), 'csv')))
				{
					// load xml
					$csv = SpoonFileCSV::fileToArray($fileFile->getTempFileName());

					// invalid csv
					if($csv === false) $fileFile->addError(BL::getError('InvalidCSV'));
				}
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// should we overwrite existing?
				$overwrite = ($this->frm->getField('overwrite_existing')->isChecked());

				// import csv
				$statistics = BackendProfilesModel::importCsv($csv, $ddmGroup->getValue(), $overwrite);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_import', array('statistics' => $statistics));

				// redefined message
				$msg = ($overwrite) ? 'profiles-imported-and-updated' : 'profiles-imported';

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=' . $msg . '&var[]=' . $statistics['count']['inserted'] . '&var[]=' . $statistics['count']['exists']);
			}
		}
	}
}
