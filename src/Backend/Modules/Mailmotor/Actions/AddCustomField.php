<?php

namespace Backend\Modules\Mailmotor\Actions;

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
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;

/**
 * This is the add-action, it will display a form to create a new custom field
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class AddCustomField extends BackendBaseActionAdd
{
    /**
     * The group record
     *
     * @var    array
     */
    private $group;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->getData();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Gets data related to custom fields
     */
    private function getData()
    {
        // get passed group ID
        $id = \SpoonFilter::getGetValue('group_id', null, 0, 'int');

        // fetch group record
        $this->group = BackendMailmotorModel::getGroup($id);

        // group doesn't exist
        if (empty($this->group)) {
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=non-existing');
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('add');
        $this->frm->addText('name');
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

            // shorten fields
            $txtName = $this->frm->getField('name');

            // validate fields
            if ($txtName->isFilled(BL::err('NameIsRequired'))) {
                if (in_array($txtName->getValue(), $this->group['custom_fields'])) {
                    $txtName->addError(
                        BL::err('CustomFieldExists')
                    );
                }
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                try {
                    // add the new item to the custom fields list
                    $this->group['custom_fields'][] = $txtName->getValue();

                    // set the group fields by flipping the custom fields array for this group
                    $groupFields = array_flip($this->group['custom_fields']);

                    // group custom fields found
                    if (!empty($groupFields)) {
                        // loop the group fields and empty every value
                        foreach ($groupFields as &$field) {
                            $field = '';
                        }
                    }

                    // addresses found and custom field delete with CM
                    BackendMailmotorCMHelper::createCustomField($txtName->getValue(), $this->group['id']);

                    // update custom fields for this group
                    BackendMailmotorModel::updateCustomFields($groupFields, $this->group['id']);
                } catch (\Exception $e) {
                    // redirect with a custom error
                    $this->redirect(
                        BackendModel::createURLForAction(
                            'CustomFields'
                        ) . '&group_id=' . $this->group['id'] . '&error=campaign-monitor-error&var=' . urlencode(
                            $e->getMessage()
                        )
                    );
                }

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction(
                        'CustomFields'
                    ) . '&group_id=' . $this->group['id'] . '&report=added&var=' . urlencode(
                        $txtName->getValue()
                    ) . '&highlight=id-' . $this->group['id']
                );
            }
        }
    }
}
