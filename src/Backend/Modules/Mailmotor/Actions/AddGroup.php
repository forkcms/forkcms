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
 * This is the add-action, it will display a form to create a new group
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class AddGroup extends BackendBaseActionAdd
{
    /**
     * Execute the action
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
        // create form
        $this->frm = new BackendForm('add');

        // add "no default group" option for radiobuttons
        $chkDefaultForLanguageValues[] = array('label' => BL::msg('NoDefault'), 'value' => '0');

        // set default for language radiobutton values
        foreach (BL::getWorkingLanguages() as $key => $value) {
            $chkDefaultForLanguageValues[] = array('label' => $value, 'value' => $key);
        }

        // create elements
        $this->frm->addText('name');
        $this->frm->addRadiobutton('default', $chkDefaultForLanguageValues);
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
            $rbtDefaultForLanguage = $this->frm->getField('default');

            // validate fields
            if ($txtName->isFilled(BL::err('NameIsRequired'))) {
                // check if the group exists by name
                if (BackendMailmotorModel::existsGroupByName($txtName->getValue())) {
                    $txtName->addError(
                        BL::err('GroupAlreadyExists')
                    );
                }
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $item['name'] = $txtName->getValue();
                $item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
                $item['language'] = $rbtDefaultForLanguage->getValue() === '0' ? null : $rbtDefaultForLanguage->getValue();
                $item['is_default'] = $rbtDefaultForLanguage->getChecked() ? 'Y' : 'N';

                // insert the item
                $item['id'] = BackendMailmotorCMHelper::insertGroup($item);

                // check if all default groups were set
                BackendMailmotorModel::checkDefaultGroups();

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add_group', array('item' => $item));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Groups') . '&report=added&var=' . urlencode(
                        $item['name']
                    ) . '&highlight=id-' . $item['id']
                );
            }
        }
    }
}
