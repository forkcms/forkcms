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
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;
use Backend\Modules\Faq\Entity\Question;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
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
        $rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => true);
        $rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

        // get categories
        $categories = BackendFaqModel::getCategories();

        // create elements
        $this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('answer');
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
        $this->frm->addDropdown('category_id', $categories);
        $this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');

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
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
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
                $question = new Question();
                $question
                    ->setMetaId($this->meta->save())
                    ->setCategory(BackendFaqModel::getCategory(
                        $this->frm->getField('category_id')->getValue()
                    ))
                    ->setUserId(BackendAuthentication::getUser()->getUserId())
                    ->setLanguage(BL::getWorkingLanguage())
                    ->setQuestion($this->frm->getField('title')->getValue())
                    ->setAnswer($this->frm->getField('answer')->getValue(true))
                    ->setIsHidden($this->frm->getField('hidden')->getValue() === 'Y')
                    ->setSequence(
                        BackendFaqModel::getMaximumSequence(
                            $this->frm->getField('category_id')->getValue()
                        ) + 1
                    )
                ;

                // save the data
                BackendFaqModel::insert($question);
                BackendTagsModel::saveTags(
                    $question->getId(),
                    $this->frm->getField('tags')->getValue(),
                    $this->URL->getModule()
                );
                BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $question));

                // add search index
                BackendSearchModel::saveIndex(
                    $this->getModule(),
                    $question->getId(),
                    array(
                        'title' => $question->getQuestion(),
                        'text' => $question->getAnswer(),
                    )
                );
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=added&var=' .
                    urlencode($question->getQuestion()) . '&highlight=' . $question->getId()
                );
            }
        }
    }
}
