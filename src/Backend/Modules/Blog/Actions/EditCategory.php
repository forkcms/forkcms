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
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendBlogModel::getCategory($this->id);
    }

    private function loadForm(): void
    {
        // create form
        $this->frm = new BackendForm('editCategory');

        // create elements
        $this->frm->addText('title', $this->record['title'], null, 'form-control title', 'form-control danger title');

        // meta object
        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);

        // set callback for generating a unique URL
        $this->meta->setURLCallback('Backend\Modules\Blog\Engine\Model', 'getURLForCategory', [$this->record['id']]);
    }

    protected function parse(): void
    {
        parent::parse();

        $this->tpl->assign('item', $this->record);

        // delete allowed?
        $this->tpl->assign(
            'allowBlogDeleteCategory',
            BackendBlogModel::deleteCategoryAllowed($this->id)
        );
    }

    private function validateForm(): void
    {
        if ($this->frm->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

            // validate meta
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item = [
                    'id' => $this->id,
                    'title' => $this->frm->getField('title')->getValue(),
                    'meta_id' => $this->meta->save(true),
                ];

                // update the item
                BackendBlogModel::updateCategory($item);

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
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule(), 'action' => 'DeleteCategory']
        );
        $this->tpl->assign('deleteForm', $deleteForm->createView());
    }
}
