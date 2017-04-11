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
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the edit synonym action, it will display a form to edit an existing synonym.
 */
class EditSynonym extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->id = $this->getId();
        $this->getData();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function getData()
    {
        $this->record = BackendSearchModel::getSynonym($this->id);
    }

    private function loadForm()
    {
        $this->frm = new BackendForm('editItem');
        $this->frm->addText('term', $this->record['term']);
        $this->frm->addText(
            'synonym',
            $this->record['synonym'],
            null,
            'form-control synonymBox',
            'form-control danger synonymBox'
        );
    }

    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('id', $this->record['id']);
        $this->tpl->assign('term', $this->record['term']);
    }

    private function validateForm()
    {
        if (!$this->frm->isSubmitted()) {
            return;
        }
        $this->frm->cleanupFields();
        $this->frm->getField('synonym')->isFilled(BL::err('SynonymIsRequired'));
        $this->frm->getField('term')->isFilled(BL::err('TermIsRequired'));
        if (BackendSearchModel::existsSynonymByTerm($this->frm->getField('term')->getValue(), $this->id)) {
            $this->frm->getField('term')->addError(BL::err('TermExists'));
        }

        if (!$this->frm->isCorrect()) {
            return;
        }

        $synonym = [
            'id' => $this->id,
            'term' => $this->frm->getField('term')->getValue(),
            'synonym' => $this->frm->getField('synonym')->getValue(),
        ];

        BackendSearchModel::updateSynonym($synonym);

        $this->redirect(
            BackendModel::createURLForAction('Synonyms') . '&report=edited-synonym&var=' . rawurlencode(
                $synonym['term']
            ) . '&highlight=row-' . $synonym['id']
        );
    }

    /**
     * @return int
     */
    private function getId(): int
    {
        $id = $this->getParameter('id', 'int');

        if ($id === 0 || !BackendSearchModel::existsSynonymById($id)) {
            $this->redirect(BackendModel::createURLForAction('Synonyms') . '&error=non-existing');
        }

        return $id;
    }
}
