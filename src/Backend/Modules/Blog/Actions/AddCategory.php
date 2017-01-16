<?php

namespace Backend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This is the add-action, it will display a form to create a new category
 */
class AddCategory extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('addCategory');
        $this->frm->addText('title', null, 255, 'form-control title', 'form-control danger title');

        // meta
        $this->meta = new BackendMeta($this->frm, null, 'title', true);

        // set callback for generating an unique URL
        $this->meta->setURLCallback('Backend\Modules\Blog\Engine\Model', 'getURLForCategory');
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
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

            // validate meta
            $this->meta->validate();

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['language'] = BL::getWorkingLanguage();
                $item['meta_id'] = $this->meta->save();

                // insert the item
                $item['id'] = BackendBlogModel::insertCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&report=added-category&var=' .
                    rawurlencode($item['title']) . '&highlight=row-' . $item['id']
                );
            }
        }
    }
}
