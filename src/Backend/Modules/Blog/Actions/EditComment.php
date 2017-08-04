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
use Backend\Core\Language\Language as BL;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This is the edit-action, it will display a form to edit an existing item
 */
class EditComment extends BackendBaseActionEdit
{
    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendBlogModel::existsComment($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exception, because somebody is fucking with our URL
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the data
     * If a revision-id was specified in the URL we load the revision and not the actual data.
     */
    private function getData(): void
    {
        // get the record
        $this->record = (array) BackendBlogModel::getComment($this->id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if (empty($this->record)) {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function loadForm(): void
    {
        // create form
        $this->form = new BackendForm('editComment');

        // create elements
        $this->form->addText('author', $this->record['author']);
        $this->form->addText('email', $this->record['email']);
        $this->form->addText('website', $this->record['website'], null);
        $this->form->addTextarea('text', $this->record['text']);

        // assign URL
        $this->template->assign(
            'itemURL',
            BackendModel::getUrlForBlock($this->getModule(), 'detail') . '/' .
            $this->record['post_url'] . '#comment-' . $this->record['post_id']
        );
        $this->template->assign('itemTitle', $this->record['post_title']);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('author')->isFilled(BL::err('AuthorIsRequired'));
            $this->form->getField('email')->isEmail(BL::err('EmailIsInvalid'));
            $this->form->getField('text')->isFilled(BL::err('FieldIsRequired'));
            if ($this->form->getField('website')->isFilled()) {
                $this->form->getField('website')->isURL(BL::err('InvalidURL'));
            }

            // no errors?
            if ($this->form->isCorrect()) {
                // build item
                $item = [
                    'id' => $this->id,
                    'status' => $this->record['status'],
                    'author' => $this->form->getField('author')->getValue(),
                    'email' => $this->form->getField('email')->getValue(),
                    'website' => $this->form->getField('website')->isFilled()
                        ? $this->form->getField('website')->getValue() : null,
                    'text' => $this->form->getField('text')->getValue(),
                ];

                // insert the item
                BackendBlogModel::updateComment($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createUrlForAction('Comments') . '&report=edited-comment&id=' .
                    $item['id'] . '&highlight=row-' . $item['id'] . '#tab' .
                    \SpoonFilter::toCamelCase($item['status'])
                );
            }
        }
    }
}
