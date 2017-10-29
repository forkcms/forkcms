<?php

namespace Backend\Modules\Faq\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * This is the add-action, it will display a form to create a new category
 */
class AddCategory extends BackendBaseActionAdd
{
    public function execute(): void
    {
        // only one category allowed, so we redirect
        if (!$this->get('fork.settings')->get('Faq', 'allow_multiple_categories', true)) {
            $this->redirect(BackendModel::createUrlForAction('Categories') . '&error=only-one-category-allowed');
        }

        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    public function parse(): void
    {
        parent::parse();

        $url = BackendModel::getUrlForBlock($this->url->getModule(), 'Category');
        $url404 = BackendModel::getUrl(BackendModel::ERROR_PAGE_ID);
        if ($url404 != $url) {
            $this->template->assign('detailURL', SITE_URL . $url);
        }
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('addCategory');
        $this->form->addText('title')->makeRequired();

        $this->meta = new BackendMeta($this->form, null, 'title', true);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->meta->setUrlCallback('Backend\Modules\Faq\Engine\Model', 'getUrlForCategory');

            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->meta->validate();

            if ($this->form->isCorrect()) {
                // build item
                $item = [];
                $item['title'] = $this->form->getField('title')->getValue();
                $item['language'] = BL::getWorkingLanguage();
                $item['meta_id'] = $this->meta->save();
                $item['sequence'] = BackendFaqModel::getMaximumCategorySequence() + 1;

                // save the data
                $item['id'] = BackendFaqModel::insertCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createUrlForAction('Categories') . '&report=added-category&var=' .
                    rawurlencode($item['title']) . '&highlight=row-' . $item['id']
                );
            }
        }
    }
}
