<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;

/**
 * This is the edit_group-action, it will display a form to edit an existing group.
 */
class EditGroup extends BackendBaseActionEdit
{
    /**
     * Info about the current group.
     *
     * @var array
     */
    private $group;

    /**
     * Execute the action.
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if ($this->id !== null && BackendProfilesModel::existsGroup($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=non-existing');
        }
    }

    /**
     * Get the data for a question
     */
    private function getData()
    {
        // get general info
        $this->group = BackendProfilesModel::getGroup($this->id);
    }

    /**
     * Load the form.
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('editGroup');
        $this->frm->addText('name', $this->group['name']);
    }

    /**
     * Parse the form.
     */
    protected function parse()
    {
        parent::parse();

        // assign the active record and additional variables
        $this->tpl->assign('group', $this->group);
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

            // get fields
            $txtName = $this->frm->getField('name');

            // name filled in?
            if ($txtName->isFilled(BL::getError('NameIsRequired'))) {
                // name already exists?
                if (BackendProfilesModel::existsGroupName($txtName->getValue(), $this->id)) {
                    // set error
                    $txtName->addError(BL::getError('GroupNameExists'));
                }
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $values['name'] = $txtName->getValue();

                // update values
                BackendProfilesModel::updateGroup($this->id, $values);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Groups') . '&report=group-saved&var=' . rawurlencode(
                        $values['name']
                    ) . '&highlight=row-' . $this->id
                );
            }
        }
    }
}
