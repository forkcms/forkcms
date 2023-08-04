<?php

namespace Backend\Modules\Blog\Actions;

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
    public function execute(): void
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('addCategory');
        $this->form->addText('title', null, 255, 'form-control title', 'form-control danger title')->makeRequired();

        // meta
        $this->meta = new BackendMeta($this->form, null, 'title', true);

        // set callback for generating an unique URL
        $this->meta->setUrlCallback('Backend\Modules\Blog\Engine\Model', 'getUrlForCategory');
    }

    protected function parse(): void
    {
        parent::parse();

        // parse base url for preview
        $url = BackendModel::getUrlForBlock($this->url->getModule(), 'Category');
        $url404 = BackendModel::getUrl(404);
        if ($url404 !== $url) {
            $this->template->assign('detailURL', SITE_URL . $url);
        }
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));

            // validate meta
            $this->meta->validate();

            // no errors?
            if ($this->form->isCorrect()) {
                // build item
                $item = [
                    'title' => $this->form->getField('title')->getValue(),
                    'language' => BL::getWorkingLanguage(),
                    'meta_id' => $this->meta->save(),
                ];

                // insert the item
                $item['id'] = BackendBlogModel::insertCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createUrlForAction('Categories') . '&report=added-category&var=' .
                    rawurlencode($item['title']) . '&highlight=row-' . $item['id']
                );
            }
        }
    }
}
