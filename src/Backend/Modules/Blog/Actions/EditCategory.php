<?php

namespace Backend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Language\Language as BL;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This is the edit category action, it will display a form to edit an existing category.
 */
class EditCategory extends BackendBaseActionEdit
{
    public function execute(): void
    {
        // get parameters
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exists
        if ($this->id !== 0 && BackendBlogModel::existsCategory($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exception, because somebody is fucking with our URL
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendBlogModel::getCategory($this->id);
    }

    private function loadForm(): void
    {
        // create form
        $this->form = new BackendForm('editCategory');

        // create elements
        $this->form->addText('title', $this->record['title'], null, 'form-control title', 'form-control danger title');

        // meta object
        $this->meta = new BackendMeta($this->form, $this->record['meta_id'], 'title', true);

        // set callback for generating a unique URL
        $this->meta->setUrlCallback('Backend\Modules\Blog\Engine\Model', 'getUrlForCategory', [$this->record['id']]);
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('item', $this->record);

        // delete allowed?
        $this->template->assign(
            'allowBlogDeleteCategory',
            BackendBlogModel::deleteCategoryAllowed($this->id)
        );
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));

            // validate meta
            $this->meta->validate();

            if ($this->form->isCorrect()) {
                // build item
                $item = [
                    'id' => $this->id,
                    'title' => $this->form->getField('title')->getValue(),
                    'meta_id' => $this->meta->save(true),
                ];

                // update the item
                BackendBlogModel::updateCategory($item);

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
