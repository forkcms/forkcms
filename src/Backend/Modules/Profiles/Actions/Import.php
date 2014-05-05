<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;

/**
 * This is the add-action, it will display a form to add a new profile.
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be
 */
class Import extends BackendBaseActionAdd
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
		if ($this->frm->isSubmitted()) {
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// get fields
			$ddmGroup = $this->frm->getField('group');
			$fileFile = $this->frm->getField('file');

			// fields filled?
			$ddmGroup->isFilled(BL::getError('FieldIsRequired'));

			// name checks
			if ($fileFile->isFilled(BL::err('FieldIsRequired'))) {
				// only xml files allowed
				if ($fileFile->isAllowedExtension(array('csv'), sprintf(BL::getError('ExtensionNotAllowed'), 'csv'))) {
					// load xml
					$csv = \SpoonFileCSV::fileToArray($fileFile->getTempFileName());

					// invalid csv
					if ($csv === false) {
					    $fileFile->addError(BL::getError('InvalidCSV'));
					}
				}
			}

			// no errors?
			if ($this->frm->isCorrect()) {
				// should we overwrite existing?
				$overwrite = ($this->frm->getField('overwrite_existing')->isChecked());

				// import csv
				$statistics = BackendProfilesModel::importCsv(
				    $csv,
				    $ddmGroup->getValue(),
				    $overwrite
				);

				// trigger event
				BackendModel::triggerEvent(
				    $this->getModule(),
				    'after_import',
				    array('statistics' => $statistics)
				);

				// init redirect url
				$redirectUrl = BackendModel::createURLForAction('index');

				// add message to redirect url
				$redirectUrl .= '&report=' . (($overwrite) ? 'profiles-imported-and-updated' : 'profiles-imported');
				$redirectUrl .= '&var[]=' . $statistics['count']['inserted'];
				$redirectUrl .= '&var[]=' . $statistics['count']['exists'];

				// everything is saved, so redirect to the overview
				$this->redirect($redirectUrl);
			}
		}
	}
}
