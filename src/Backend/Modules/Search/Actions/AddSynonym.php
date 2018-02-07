<?php

namespace Backend\Modules\Search\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use App\Component\Locale\BackendLanguage;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

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
        $this->form->getField('synonym')->isFilled(BackendLanguage::err('SynonymIsRequired'));
        $this->form->getField('term')->isFilled(BackendLanguage::err('TermIsRequired'));
        if (BackendSearchModel::existsSynonymByTerm($this->form->getField('term')->getValue())) {
            $this->form->getField('term')->addError(BackendLanguage::err('TermExists'));
        }

        if (!$this->form->isCorrect()) {
            return;
        }

        $synonym = [
            'term' => $this->form->getField('term')->getValue(),
            'synonym' => $this->form->getField('synonym')->getValue(),
            'language' => BackendLanguage::getWorkingLanguage(),
        ];

        $id = BackendSearchModel::insertSynonym($synonym);

        $this->redirect(
            BackendModel::createUrlForAction('Synonyms') . '&report=added-synonym&var=' . rawurlencode(
                $synonym['term']
            ) . '&highlight=row-' . $id
        );
    }
}
