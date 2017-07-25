<?php

namespace Backend\Modules\Search\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the edit synonym action, it will display a form to edit an existing synonym.
 */
class EditSynonym extends BackendBaseActionEdit
{
    public function execute(): void
    {
        parent::execute();

        $this->id = $this->getId();
        $this->getData();
        $this->loadForm();
        $this->validateForm();
        $this->loadDeleteForm();
        $this->parse();
        $this->display();
    }

    private function getData(): void
    {
        $this->record = BackendSearchModel::getSynonym($this->id);
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('editItem');
        $this->form->addText('term', $this->record['term']);
        $this->form->addText(
            'synonym',
            $this->record['synonym'],
            null,
            'form-control synonymBox',
            'form-control danger synonymBox'
        );
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('id', $this->record['id']);
        $this->template->assign('term', $this->record['term']);
    }

    private function validateForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }
        $this->form->cleanupFields();
        $this->form->getField('synonym')->isFilled(BL::err('SynonymIsRequired'));
        $this->form->getField('term')->isFilled(BL::err('TermIsRequired'));
        if (BackendSearchModel::existsSynonymByTerm($this->form->getField('term')->getValue(), $this->id)) {
            $this->form->getField('term')->addError(BL::err('TermExists'));
        }

        if (!$this->form->isCorrect()) {
            return;
        }

        $synonym = [
            'id' => $this->id,
            'term' => $this->form->getField('term')->getValue(),
            'synonym' => $this->form->getField('synonym')->getValue(),
        ];

        BackendSearchModel::updateSynonym($synonym);

        $this->redirect(
            BackendModel::createUrlForAction('Synonyms') . '&report=edited-synonym&var=' . rawurlencode(
                $synonym['term']
            ) . '&highlight=row-' . $synonym['id']
        );
    }

    private function getId(): int
    {
        $id = $this->getRequest()->query->getInt('id');

        if ($id === 0 || !BackendSearchModel::existsSynonymById($id)) {
            $this->redirect(BackendModel::createUrlForAction('Synonyms') . '&error=non-existing');
        }

        return $id;
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule(), 'action' => 'DeleteSynonym']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
