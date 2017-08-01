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
use Backend\Form\Type\DeleteType;
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

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exists
        if ($this->id !== 0 && BackendFaqModel::exists($this->id)) {
            parent::execute();

            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();
            $this->loadDeleteFeedbackForm();

            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = (array) BackendFaqModel::get($this->id);
        $this->feedback = BackendFaqModel::getAllFeedbackForQuestion($this->id);
    }

    private function loadForm(): void
    {
        // get values for the form
        $rbtHiddenValues = [
            ['label' => BL::lbl('Hidden'), 'value' => 1],
            ['label' => BL::lbl('Published'), 'value' => 0],
        ];
        $categories = BackendFaqModel::getCategories();

        // create form
        $this->form = new BackendForm('edit');
        $this->form->addText('title', $this->record['question'], null, 'form-control title', 'form-control danger title');
        $this->form->addEditor('answer', $this->record['answer']);
        $this->form->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
        $this->form->addDropdown('category_id', $categories, $this->record['category_id']);
        $this->form->addText(
            'tags',
            BackendTagsModel::getTags($this->url->getModule(), $this->record['id']),
            null,
            'form-control js-tags-input',
            'form-control danger js-tags-input'
        );

        $this->meta = new BackendMeta($this->form, $this->record['meta_id'], 'title', true);
    }

    protected function parse(): void
    {
        parent::parse();

        // get url
        $url = BackendModel::getUrlForBlock($this->url->getModule(), 'Detail');
        $url404 = BackendModel::getUrl(404);
        if ($url404 != $url) {
            $this->template->assign('detailURL', SITE_URL . $url);
        }

        // assign the active record and additional variables
        $this->template->assign('item', $this->record);
        $this->template->assign('feedback', $this->feedback);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->meta->setUrlCallback('Backend\Modules\Faq\Engine\Model', 'getUrl', [$this->record['id']]);

            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('QuestionIsRequired'));
            $this->form->getField('answer')->isFilled(BL::err('AnswerIsRequired'));
            $this->form->getField('category_id')->isFilled(BL::err('CategoryIsRequired'));
            $this->meta->validate();

            if ($this->form->isCorrect()) {
                // build item
                $item = [];
                $item['id'] = $this->id;
                $item['meta_id'] = $this->meta->save(true);
                $item['category_id'] = $this->form->getField('category_id')->getValue();
                $item['language'] = $this->record['language'];
                $item['question'] = $this->form->getField('title')->getValue();
                $item['answer'] = $this->form->getField('answer')->getValue(true);
                $item['hidden'] = $this->form->getField('hidden')->getValue();

                // update the item
                BackendFaqModel::update($item);
                BackendTagsModel::saveTags(
                    $item['id'],
                    $this->form->getField('tags')->getValue(),
                    $this->url->getModule()
                );

                // edit search index
                BackendSearchModel::saveIndex(
                    $this->getModule(),
                    $item['id'],
                    [
                        'title' => $item['question'],
                        'text' => $item['answer'],
                    ]
                );

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createUrlForAction('Index') . '&report=saved&var=' .
                    rawurlencode($item['question']) . '&highlight=' . $item['id']
                );
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule()]
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }

    private function loadDeleteFeedbackForm(): void
    {
        $deleteFeedbackForm = $this->createForm(
            DeleteType::class,
            null,
            ['module' => $this->getModule(), 'action' => 'DeleteFeedback']
        );
        $this->template->assign('deleteFeedbackForm', $deleteFeedbackForm->createView());
    }
}
