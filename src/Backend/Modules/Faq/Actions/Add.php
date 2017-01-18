<?php

namespace Backend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This is the add-action, it will display a form to create a new item
 */
class Add extends BackendBaseActionAdd
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
        // create form
        $this->frm = new BackendForm('add');

        // set hidden values
        $rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
        $rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

        // get categories
        $categories = BackendFaqModel::getCategories();

        // create elements
        $this->frm->addText('title', null, null, 'form-control title', 'form-control danger title');
        $this->frm->addEditor('answer');
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
        $this->frm->addDropdown('category_id', $categories);
        $this->frm->addText('tags', null, null, 'form-control js-tags-input', 'form-control danger js-tags-input');

        // meta
        $this->meta = new BackendMeta($this->frm, null, 'title', true);
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'Detail');
        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('QuestionIsRequired'));
            $this->frm->getField('answer')->isFilled(BL::err('AnswerIsRequired'));
            $this->frm->getField('category_id')->isFilled(BL::err('CategoryIsRequired'));
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item['meta_id'] = $this->meta->save();
                $item['category_id'] = $this->frm->getField('category_id')->getValue();
                $item['user_id'] = BackendAuthentication::getUser()->getUserId();
                $item['language'] = BL::getWorkingLanguage();
                $item['question'] = $this->frm->getField('title')->getValue();
                $item['answer'] = $this->frm->getField('answer')->getValue(true);
                $item['created_on'] = BackendModel::getUTCDate();
                $item['hidden'] = $this->frm->getField('hidden')->getValue();
                $item['sequence'] = BackendFaqModel::getMaximumSequence(
                    $this->frm->getField('category_id')->getValue()
                ) + 1;

                // save the data
                $item['id'] = BackendFaqModel::insert($item);
                BackendTagsModel::saveTags(
                    $item['id'],
                    $this->frm->getField('tags')->getValue(),
                    $this->URL->getModule()
                );

                // add search index
                BackendSearchModel::saveIndex(
                    $this->getModule(),
                    $item['id'],
                    array(
                        'title' => $item['question'],
                        'text' => $item['answer'],
                    )
                );
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=added&var=' .
                    rawurlencode($item['question']) . '&highlight=' . $item['id']
                );
            }
        }
    }
}
