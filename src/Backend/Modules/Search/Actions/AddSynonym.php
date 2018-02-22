<?php

namespace App\Backend\Modules\Search\Actions;

use App\Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use App\Backend\Core\Engine\Form as BackendForm;
use App\Backend\Core\Language\Language as BL;
use App\Backend\Core\Engine\Model as BackendModel;
use App\Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the add-action, it will display a form to create a new synonym
 */
class AddSynonym extends BackendBaseActionAdd
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
        $this->form = new BackendForm('addItem');

        $this->form->addText('term', null, 255);
        $this->form->addText('synonym', null, null, 'form-control synonymBox', 'form-control danger synonymBox');
    }

    private function validateForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }

        $this->form->cleanupFields();
        $this->form->getField('synonym')->isFilled(BL::err('SynonymIsRequired'));
        $this->form->getField('term')->isFilled(BL::err('TermIsRequired'));
        if (BackendSearchModel::existsSynonymByTerm($this->form->getField('term')->getValue())) {
            $this->form->getField('term')->addError(BL::err('TermExists'));
        }

        if (!$this->form->isCorrect()) {
            return;
        }

        $synonym = [
            'term' => $this->form->getField('term')->getValue(),
            'synonym' => $this->form->getField('synonym')->getValue(),
            'language' => BL::getWorkingLanguage(),
        ];

        $id = BackendSearchModel::insertSynonym($synonym);

        $this->redirect(
            BackendModel::createUrlForAction('Synonyms') . '&report=added-synonym&var=' . rawurlencode(
                $synonym['term']
            ) . '&highlight=row-' . $id
        );
    }
}
