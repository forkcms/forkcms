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
    public function execute(): void
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        // get group values for dropdown
        $ddmValues = BackendProfilesModel::getGroupsForDropDown(0);

        // create form and elements
        $this->form = new BackendForm('import');
        $this->form->addDropdown('group', $ddmValues);
        $this->form->addFile('file');
        $this->form->addCheckbox('overwrite_existing');
    }

    private function validateForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }
        $this->form->cleanupFields();

        // get fields
        $ddmGroup = $this->form->getField('group');
        $fileFile = $this->form->getField('file');
        $csv = [];

        // validate input
        $ddmGroup->isFilled(BL::getError('FieldIsRequired'));
        if ($fileFile->isFilled(BL::err('FieldIsRequired'))) {
            if ($fileFile->isAllowedExtension(['csv'], sprintf(BL::getError('ExtensionNotAllowed'), 'csv'))) {
                $csv = Csv::fileToArray($fileFile->getTempFileName());
                if ($csv === false) {
                    $fileFile->addError(BL::getError('InvalidCSV'));
                }
            }
        }

        if (!$this->form->isCorrect()) {
            return;
        }

        // import the profiles
        $overwrite = $this->form->getField('overwrite_existing')->isChecked();
        $statistics = BackendProfilesModel::importCsv(
            $csv,
            $ddmGroup->getValue(),
            $overwrite
        );

        // build redirect url with the right message
        $redirectUrl = BackendModel::createUrlForAction('index') . '&report=';
        $redirectUrl .= $overwrite ?
            'profiles-imported-and-updated' :
            'profiles-imported';
        $redirectUrl .= '&var[]=' . $statistics['count']['inserted'];
        $redirectUrl .= '&var[]=' . $statistics['count']['exists'];

        // everything is saved, so redirect to the overview
        $this->redirect($redirectUrl);
    }
}
