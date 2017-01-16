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

/**
 * This is the add_group-action, it will display a form to add a group for profiles.
 */
class AddGroup extends BackendBaseActionAdd
{
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
     * Load the form.
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('addGroup');
        $this->frm->addText('name');
    }

    /**
     * Validate the form.
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // get field
            /** @var $txtName \SpoonFormText */
            $txtName = $this->frm->getField('name');

            // name filled in?
            if ($txtName->isFilled(BL::getError('NameIsRequired'))) {
                // name exists?
                if (BackendProfilesModel::existsGroupName($txtName->getValue())) {
                    // set error
                    $txtName->addError(BL::getError('GroupNameExists'));
                }
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $values['name'] = $txtName->getValue();

                // insert values
                $id = BackendProfilesModel::insertGroup($values);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Groups') . '&report=group-added&var=' . rawurlencode(
                        $values['name']
                    ) . '&highlight=row-' . $id
                );
            }
        }
    }
}
