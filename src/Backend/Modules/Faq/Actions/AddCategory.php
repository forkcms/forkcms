<?php

namespace Backend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

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
    /**
     * Execute the action
     */
    public function execute()
    {
        // only one category allowed, so we redirect
        if (!$this->get('fork.settings')->get('Faq', 'allow_multiple_categories', true)) {
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=only-one-category-allowed');
        }

        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    public function parse()
    {
        parent::parse();

        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'Category');
        $url404 = BackendModel::getURL(404);
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('addCategory');
        $this->frm->addText('title');

        $this->meta = new BackendMeta($this->frm, null, 'title', true);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->meta->setURLCallback('Backend\Modules\Faq\Engine\Model', 'getURLForCategory');

            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['language'] = BL::getWorkingLanguage();
                $item['meta_id'] = $this->meta->save();
                $item['sequence'] = BackendFaqModel::getMaximumCategorySequence() + 1;

                // save the data
                $item['id'] = BackendFaqModel::insertCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&report=added-category&var=' .
                    rawurlencode($item['title']) . '&highlight=row-' . $item['id']
                );
            }
        }
    }
}
