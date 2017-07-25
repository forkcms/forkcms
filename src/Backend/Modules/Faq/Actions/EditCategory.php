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
use Backend\Form\Type\DeleteType;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

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
            $this->redirect(BackendModel::createUrlForAction('Categories') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendFaqModel::getCategory($this->id);
    }

    private function loadForm(): void
    {
        // create form
        $this->form = new BackendForm('editCategory');
        $this->form->addText('title', $this->record['title']);

        $this->meta = new BackendMeta($this->form, $this->record['meta_id'], 'title', true);
    }

    protected function parse(): void
    {
        parent::parse();

        // assign the data
        $this->template->assign('item', $this->record);
        $this->template->assign(
            'showFaqDeleteCategory',
            (
                BackendFaqModel::deleteCategoryAllowed($this->id) &&
                BackendAuthentication::isAllowedAction('DeleteCategory')
            )
        );

        $url = BackendModel::getUrlForBlock($this->url->getModule(), 'Category');
        $url404 = BackendModel::getUrl(404);
        if ($url404 != $url) {
            $this->template->assign('detailURL', SITE_URL . $url);
        }
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->meta->setUrlCallback(
                'Backend\Modules\Faq\Engine\Model',
                'getUrlForCategory',
                [$this->record['id']]
            );

            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->meta->validate();

            if ($this->form->isCorrect()) {
                // build item
                $item = [];
                $item['id'] = $this->id;
                $item['language'] = $this->record['language'];
                $item['title'] = $this->form->getField('title')->getValue();
                $item['extra_id'] = $this->record['extra_id'];
                $item['meta_id'] = $this->meta->save(true);

                // update the item
                BackendFaqModel::updateCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createUrlForAction('Categories') . '&report=edited-category&var=' .
                    rawurlencode($item['title']) . '&highlight=row-' . $item['id']
                );
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule(), 'action' => 'DeleteCategory']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
