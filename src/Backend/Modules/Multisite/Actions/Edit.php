<?php

namespace Backend\Modules\Multisite\Actions;

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Multisite\Engine\Model as MultisiteModel;
use Backend\Modules\Multisite\Engine\LanguageCheckboxes;

/**
 * Backend Edit action for the Multisite module.
 * It will display a form with initial data, validate it when posted and update
 * the model when the form is valid.
 *
 * @author <per@wijs.be>
 * @author Wouter Sioen <wouter@wijs.be>
 */
class Edit extends ActionEdit
{
    public function execute()
    {
        parent::execute();
        $this->loadData();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadData()
    {
        $this->id = $this->getParameter('id', 'int');
        if ($this->id === null || !MultisiteModel::exists($this->id)) {
            $this->redirect(
                Model::createURLForAction('Index') .
                '&error=non-existing'
            );
        }

        $this->record = MultisiteModel::get($this->id);
    }

    private function loadForm()
    {
        $this->frm = new Form('edit');
        $this->frm->addText('domain', $this->record['domain']);
        $this->frm->addCheckbox(
            'is_active',
            $this->record['is_active'] === 'Y'
        );
        $this->frm->addCheckbox(
            'is_viewable',
            $this->record['is_viewable'] === 'Y'
        );
        $this->chkLanguages = LanguageCheckboxes::addToForm(
            $this->frm,
            MultisiteModel::getLanguages($this->id)
        );
    }

    protected function parse()
    {
        parent::parse();
        $this->frm->parse($this->tpl);
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign('languages', $this->chkLanguages);
    }

    private function save($fields)
    {
        $item = array(
            'domain'      => $fields['domain']->getValue(),
            'languages'   => LanguageCheckboxes::getValues($this->frm),
            'is_active'   => $fields['is_active']->getChecked() ? 'Y' : 'N',
            'is_viewable' => $fields['is_viewable']->getChecked() ? 'Y' : 'N',
        );
        MultisiteModel::update($item, $this->id);
        $this->redirect(
            Model::createURLForAction('Index') .
                '&report=edited' .
                '&var=' . urlencode($item['domain']) .
                '&highlight=row-' . $this->id
        );
    }

    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();
            $fields = $this->frm->getFields();
            $fields['domain']->isFilled(Language::err('FieldIsRequired'));

            if ($this->frm->isCorrect()) {
                $this->save($fields);
            }
        }
    }
}
