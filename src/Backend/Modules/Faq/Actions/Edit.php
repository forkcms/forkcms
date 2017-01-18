<?php

namespace Backend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This is the edit-action, it will display a form to edit an existing item
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * @var array
     */
    private $feedback;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if ($this->id !== null && BackendFaqModel::exists($this->id)) {
            parent::execute();

            $this->getData();
            $this->loadForm();
            $this->validateForm();

            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->record = (array) BackendFaqModel::get($this->id);
        $this->feedback = BackendFaqModel::getAllFeedbackForQuestion($this->id);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // get values for the form
        $rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');
        $rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');
        $categories = BackendFaqModel::getCategories();

        // create form
        $this->frm = new BackendForm('edit');
        $this->frm->addText('title', $this->record['question'], null, 'form-control title', 'form-control danger title');
        $this->frm->addEditor('answer', $this->record['answer']);
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
        $this->frm->addDropdown('category_id', $categories, $this->record['category_id']);
        $this->frm->addText(
            'tags',
            BackendTagsModel::getTags($this->URL->getModule(), $this->record['id']),
            null,
            'form-control js-tags-input',
            'form-control danger js-tags-input'
        );

        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'Detail');
        $url404 = BackendModel::getURL(404);
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }

        // assign the active record and additional variables
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign('feedback', $this->feedback);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->meta->setURLCallback('Backend\Modules\Faq\Engine\Model', 'getURL', array($this->record['id']));

            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('QuestionIsRequired'));
            $this->frm->getField('answer')->isFilled(BL::err('AnswerIsRequired'));
            $this->frm->getField('category_id')->isFilled(BL::err('CategoryIsRequired'));
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = $this->id;
                $item['meta_id'] = $this->meta->save(true);
                $item['category_id'] = $this->frm->getField('category_id')->getValue();
                $item['language'] = $this->record['language'];
                $item['question'] = $this->frm->getField('title')->getValue();
                $item['answer'] = $this->frm->getField('answer')->getValue(true);
                $item['hidden'] = $this->frm->getField('hidden')->getValue();

                // update the item
                BackendFaqModel::update($item);
                BackendTagsModel::saveTags(
                    $item['id'],
                    $this->frm->getField('tags')->getValue(),
                    $this->URL->getModule()
                );

                // edit search index
                BackendSearchModel::saveIndex(
                    $this->getModule(),
                    $item['id'],
                    array(
                        'title' => $item['question'],
                        'text' => $item['answer'],
                    )
                );

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=saved&var=' .
                    rawurlencode($item['question']) . '&highlight=' . $item['id']
                );
            }
        }
    }
}
