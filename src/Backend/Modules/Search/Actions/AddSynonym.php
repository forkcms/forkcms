<?php

namespace Backend\Modules\Search\Actions;

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
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the add-action, it will display a form to create a new synonym
 */
class AddSynonym extends BackendBaseActionAdd
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
        $this->frm = new BackendForm('addItem');

        // create elements
        $this->frm->addText('term', null, 255);
        $this->frm->addText('synonym', null, null, 'form-control synonymBox', 'form-control danger synonymBox');
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

            // validate field
            $this->frm->getField('synonym')->isFilled(BL::err('SynonymIsRequired'));
            $this->frm->getField('term')->isFilled(BL::err('TermIsRequired'));
            if (BackendSearchModel::existsSynonymByTerm($this->frm->getField('term')->getValue())) {
                $this->frm->getField(
                    'term'
                )->addError(BL::err('TermExists'));
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $item = array();
                $item['term'] = $this->frm->getField('term')->getValue();
                $item['synonym'] = $this->frm->getField('synonym')->getValue();
                $item['language'] = BL::getWorkingLanguage();

                // insert the item
                $id = BackendSearchModel::insertSynonym($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Synonyms') . '&report=added-synonym&var=' . rawurlencode(
                        $item['term']
                    ) . '&highlight=row-' . $id
                );
            }
        }
    }
}
