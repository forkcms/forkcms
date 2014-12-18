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
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * @var	array
     */
    private $feedback;

    /**
     * Execute the action
     */
    public function execute()
    {
        // does the item exists
        parent::execute();

        $this->getData();
        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->id = $this->getParameter('id', 'int');

        $this->record = BackendFaqModel::get($this->id);
        if ($this->id === null || empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        $this->feedback = array();//BackendFaqModel::getAllFeedbackForQuestion($this->id);
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
        $this->frm->addText('title', $this->record->getQuestion(), null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('answer', $this->record->getAnswer());
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record->getIsHidden() ? 'Y' : 'N');
        $this->frm->addDropdown('category_id', $categories, $this->record->getCategory()->getId());
        $this->frm->addText(
            'tags',
            BackendTagsModel::getTags($this->URL->getModule(), $this->record->getId()),
            null,
            'inputText tagBox',
            'inputTextError tagBox'
        );

        $this->meta = new BackendMeta($this->frm, $this->record->getMetaId(), 'title', true);
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
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
            $this->meta->setUrlCallback('Backend\Modules\Faq\Engine\Model', 'getURL', array($this->record->getId()));

            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('QuestionIsRequired'));
            $this->frm->getField('answer')->isFilled(BL::err('AnswerIsRequired'));
            $this->frm->getField('category_id')->isFilled(BL::err('CategoryIsRequired'));
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $this->record
                    ->setMetaId($this->meta->save(true))
                    ->setCategory(BackendFaqModel::getCategory(
                        $this->frm->getField('category_id')->getValue()
                    ))
                    ->setQuestion($this->frm->getField('title')->getValue())
                    ->setAnswer($this->frm->getField('answer')->getValue(true))
                    ->setIsHidden($this->frm->getField('hidden')->getValue() === 'Y')
                ;

                // update the item
                BackendFaqModel::update($this->record);
                BackendTagsModel::saveTags(
                    $this->record->getId(),
                    $this->frm->getField('tags')->getValue(),
                    $this->URL->getModule()
                );
                BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $this->record));

                // edit search index
                BackendSearchModel::saveIndex(
                    $this->getModule(),
                    $this->record->getId(),
                    array(
                        'title' => $this->record->getQuestion(),
                        'text' => $this->record->getAnswer(),
                    )
                );

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=saved&var=' .
                    urlencode($this->record->getQuestion()) . '&highlight=' . $this->record->getId()
                );
            }
        }
    }
}
