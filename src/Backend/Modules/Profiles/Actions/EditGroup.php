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
use Backend\Modules\Profiles\Form\GroupDeleteType;

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

    public function execute(): void
    {
        // get parameters
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exists
        if ($this->id !== 0 && BackendProfilesModel::existsGroup($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();
            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        // get general info
        $this->group = BackendProfilesModel::getGroup($this->id);
    }

    private function loadForm(): void
    {
        $this->frm = new BackendForm('editGroup');
        $this->frm->addText('name', $this->group['name']);
    }

    protected function parse(): void
    {
        parent::parse();

        // assign the active record and additional variables
        $this->tpl->assign('group', $this->group);
    }

    private function validateForm(): void
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
                $values = ['name' => $txtName->getValue()];

                // update values
                BackendProfilesModel::updateGroup($this->id, ['name' => $values]);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Groups') . '&report=group-saved&var=' . rawurlencode(
                        $values['name']
                    ) . '&highlight=row-' . $this->id
                );
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(GroupDeleteType::class, ['id' => $this->group['id']]);
        $this->tpl->assign('deleteForm', $deleteForm->createView());
    }
}
