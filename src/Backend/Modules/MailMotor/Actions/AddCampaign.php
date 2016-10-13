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
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;

/**
 * This is the add-action, it will display a form to create a new campaign
 */
class AddCampaign extends BackendBaseActionAdd
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
            $txtName->isFilled(BL::err('NameIsRequired'));

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $item['name'] = $txtName->getValue();
                $item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

                // insert the item
                $item['id'] = BackendMailmotorModel::insertCampaign($item);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add_campaign', array('item' => $item));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Campaigns') . '&report=added&var=' . rawurlencode(
                        $item['name']
                    ) . '&highlight=id-' . $item['id']
                );
            }
        }
    }
}
