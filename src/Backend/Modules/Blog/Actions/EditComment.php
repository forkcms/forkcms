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
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendBlogModel::existsComment($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exception, because somebody is fucking with our URL
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the data
     * If a revision-id was specified in the URL we load the revision and not the actual data.
     */
    private function getData()
    {
        // get the record
        $this->record = (array) BackendBlogModel::getComment($this->id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if (empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('editComment');

        // create elements
        $this->frm->addText('author', $this->record['author']);
        $this->frm->addText('email', $this->record['email']);
        $this->frm->addText('website', $this->record['website'], null);
        $this->frm->addTextarea('text', $this->record['text']);

        // assign URL
        $this->tpl->assign(
            'itemURL',
            BackendModel::getURLForBlock($this->getModule(), 'detail') . '/' .
            $this->record['post_url'] . '#comment-' . $this->record['post_id']
        );
        $this->tpl->assign('itemTitle', $this->record['post_title']);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('author')->isFilled(BL::err('AuthorIsRequired'));
            $this->frm->getField('email')->isEmail(BL::err('EmailIsInvalid'));
            $this->frm->getField('text')->isFilled(BL::err('FieldIsRequired'));
            if ($this->frm->getField('website')->isFilled()) {
                $this->frm->getField('website')->isURL(BL::err('InvalidURL'));
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = $this->id;
                $item['status'] = $this->record['status'];
                $item['author'] = $this->frm->getField('author')->getValue();
                $item['email'] = $this->frm->getField('email')->getValue();
                $item['website'] = ($this->frm->getField('website')->isFilled()) ? $this->frm->getField('website')->getValue() : null;
                $item['text'] = $this->frm->getField('text')->getValue();

                // insert the item
                BackendBlogModel::updateComment($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Comments') . '&report=edited-comment&id=' .
                    $item['id'] . '&highlight=row-' . $item['id'] . '#tab' .
                    \SpoonFilter::toCamelCase($item['status'])
                );
            }
        }
    }
}
