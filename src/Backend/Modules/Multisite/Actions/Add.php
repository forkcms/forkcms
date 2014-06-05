<?php

namespace Backend\Modules\Multisite\Actions;

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Multisite\Engine\Model as MultisiteModel;
use Backend\Modules\Multisite\Engine\LanguageCheckboxes;

/**
 * Backend Add action for the Multisite module.
 * It will display a form, validate it when posted and update the model when
 * the form is valid.
 *
 * @author <per@wijs.be>
 * @author <wouter@wijs.be>
 */
class Add extends ActionAdd
{
    /**
     * @var array
     */
    private $chkLanguages;

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
        $this->frm = new Form('add');
        $this->frm->addText('domain');
        $this->frm->addCheckbox('is_active', true);
        $this->frm->addCheckbox('is_viewable', true);
        $this->chkLanguages = LanguageCheckboxes::addToForm($this->frm);
    }

    protected function parse()
    {
        $this->frm->parse($this->tpl);
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
        $id = MultisiteModel::add($item);
        $this->redirect(
            Model::createURLForAction('Index') .
                '&report=added' .
                '&var=' . urlencode($item['domain']) .
                '&highlight=row-' . $id
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
