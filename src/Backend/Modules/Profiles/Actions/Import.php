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
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;
use Backend\Core\Engine\Csv;

/**
 * This is the add-action, it will display a form to add a new profile.
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

        // create form and elements
        $this->frm = new BackendForm('import');
        $this->frm->addDropdown('group', $ddmValues);
        $this->frm->addFile('file');
        $this->frm->addCheckbox('overwrite_existing');
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // get fields
            $ddmGroup = $this->frm->getField('group');
            $fileFile = $this->frm->getField('file');
            $csv = array();

            // validate input
            $ddmGroup->isFilled(BL::getError('FieldIsRequired'));
            if ($fileFile->isFilled(BL::err('FieldIsRequired'))) {
                if ($fileFile->isAllowedExtension(
                    array('csv'),
                    sprintf(BL::getError('ExtensionNotAllowed'), 'csv')
                )
                ) {
                    $csv = Csv::fileToArray($fileFile->getTempFileName());
                    if ($csv === false) {
                        $fileFile->addError(BL::getError('InvalidCSV'));
                    }
                }
            }

            if ($this->frm->isCorrect()) {
                // import the profiles
                $overwrite = $this->frm->getField('overwrite_existing')->isChecked();
                $statistics = BackendProfilesModel::importCsv(
                    $csv,
                    $ddmGroup->getValue(),
                    $overwrite
                );

                // build redirect url with the right message
                $redirectUrl = BackendModel::createURLForAction('index') . '&report=';
                $redirectUrl .= ($overwrite) ?
                    'profiles-imported-and-updated' :
                    'profiles-imported'
                ;
                $redirectUrl .= '&var[]=' . $statistics['count']['inserted'];
                $redirectUrl .= '&var[]=' . $statistics['count']['exists'];

                // everything is saved, so redirect to the overview
                $this->redirect($redirectUrl);
            }
        }
    }
}
