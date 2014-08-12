<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;

/**
 * This is the edit-action, it will display a form to edit a group
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class EditGroup extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if (BackendMailmotorModel::existsGroup($this->id)) {
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
     * Get the data
     */
    private function getData()
    {
        // get the record
        $this->record = (array) BackendMailmotorModel::getGroup($this->id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if (empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=non-existing');
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');

        // add "no default group" option for radiobuttons
        $chkDefaultForLanguageValues[] = array('label' => BL::msg('NoDefault'), 'value' => '0');

        // set default for language radiobutton values
        $multisite = BackendModel::get('multisite');
        foreach ($multisite->getSites() as $siteId => $domain) {
            foreach ($multisite->getLanguageList($siteId, true) as $language) {
                $chkDefaultForLanguageValues[] = array(
                    'label' => $domain . ': ' . BL::lbl(strtoupper($language)),
                    'value' => $siteId . '-' . $language
                );
            }
        }

        // create elements
        $default = ($this->record['is_default'] === 'Y') ?
            $this->record['site_id'] . '-' . $this->record['language'] :
            '0'
        ;
        $this->frm->addText('name', $this->record['name']);
        $this->frm->addRadiobutton('default', $chkDefaultForLanguageValues, $default);
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // assign the active record and additional variables
        $this->tpl->assign('group', $this->record);
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
                if ($txtName->getValue() != $this->record['name'] &&
                    BackendMailmotorModel::existsGroupByName($txtName->getValue())
                ) {
                    $txtName->addError(BL::err('GroupAlreadyExists'));
                }
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = $this->id;
                $item['name'] = $txtName->getValue();
                $item['is_default'] = $rbtDefaultForLanguage->getChecked() ? 'Y' : 'N';

                if ($rbtDefaultForLanguage->getChecked()) {
                    // language and site_id are stored in the value seperated by a dash
                    list($item['site_id'], $item['language']) = explode('-', $rbtDefaultForLanguage->getValue());
                }

                // update the item
                BackendMailmotorCMHelper::updateGroup($item);

                // check if all default groups were set
                BackendMailmotorModel::checkDefaultGroups();

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_edit_group', array('item' => $item));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Groups') . '&report=edited&var=' . urlencode(
                        $item['name']
                    ) . '&highlight=id-' . $item['id']
                );
            }
        }
    }
}
