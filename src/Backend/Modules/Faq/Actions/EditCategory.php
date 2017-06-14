<?php

namespace Backend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;
use Backend\Modules\Faq\Form\FaqCategoryDeleteType;

/**
 * This is the edit category action, it will display a form to edit an existing category.
 */
class EditCategory extends BackendBaseActionEdit
{
    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist?
        if ($this->id !== 0 && BackendFaqModel::existsCategory($this->id)) {
            parent::execute();

            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();

            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendFaqModel::getCategory($this->id);
    }

    private function loadForm(): void
    {
        // create form
        $this->frm = new BackendForm('editCategory');
        $this->frm->addText('title', $this->record['title']);

        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
    }

    protected function parse(): void
    {
        parent::parse();

        // assign the data
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign(
            'showFaqDeleteCategory',
            (
                BackendFaqModel::deleteCategoryAllowed($this->id) &&
                BackendAuthentication::isAllowedAction('DeleteCategory')
            )
        );

        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'Category');
        $url404 = BackendModel::getURL(404);
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }
    }

    private function validateForm(): void
    {
        if ($this->frm->isSubmitted()) {
            $this->meta->setURLCallback(
                'Backend\Modules\Faq\Engine\Model',
                'getURLForCategory',
                [$this->record['id']]
            );

            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item = [];
                $item['id'] = $this->id;
                $item['language'] = $this->record['language'];
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['extra_id'] = $this->record['extra_id'];
                $item['meta_id'] = $this->meta->save(true);

                // update the item
                BackendFaqModel::updateCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&report=edited-category&var=' .
                    rawurlencode($item['title']) . '&highlight=row-' . $item['id']
                );
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(FaqCategoryDeleteType::class, ['id' => $this->record['id']]);
        $this->tpl->assign('deleteForm', $deleteForm->createView());
    }
}
