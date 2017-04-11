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
    public function execute()
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm()
    {
        $this->frm = new BackendForm('addItem');

        $this->frm->addText('term', null, 255);
        $this->frm->addText('synonym', null, null, 'form-control synonymBox', 'form-control danger synonymBox');
    }

    private function validateForm()
    {
        if (!$this->frm->isSubmitted()) {
            return;
        }

        $this->frm->cleanupFields();
        $this->frm->getField('synonym')->isFilled(BL::err('SynonymIsRequired'));
        $this->frm->getField('term')->isFilled(BL::err('TermIsRequired'));
        if (BackendSearchModel::existsSynonymByTerm($this->frm->getField('term')->getValue())) {
            $this->frm->getField('term')->addError(BL::err('TermExists'));
        }

        if (!$this->frm->isCorrect()) {
            return;
        }

        $synonym = [
            'term' => $this->frm->getField('term')->getValue(),
            'synonym' => $this->frm->getField('synonym')->getValue(),
            'language' => BL::getWorkingLanguage(),
        ];

        $id = BackendSearchModel::insertSynonym($synonym);

        $this->redirect(
            BackendModel::createURLForAction('Synonyms') . '&report=added-synonym&var=' . rawurlencode(
                $synonym['term']
            ) . '&highlight=row-' . $id
        );
    }
}
