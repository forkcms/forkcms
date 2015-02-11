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
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Entity\Meta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;
use Backend\Modules\Faq\Entity\Category;

/**
 * This is the add-action, it will display a form to create a new category
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author SIESQO <info@siesqo.be>
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class AddCategory extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // only one category allowed, so we redirect
        if (!BackendModel::getModuleSetting('Faq', 'allow_multiple_categories', true)) {
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=only-one-category-allowed');
        }

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
        $this->frm->addText('title');

        $this->meta = new BackendMeta($this->frm, new Meta(), 'title', true);
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
                $category = new Category();
                $category
                    ->setTitle($this->frm->getField('title')->getValue())
                    ->setLanguage(BL::getWorkingLanguage())
                    ->setMeta($this->meta->save())
                    ->setSequence(BackendFaqModel::getMaximumCategorySequence() + 1)
                ;

                // save the data
                BackendFaqModel::insertCategory($category);
                BackendModel::triggerEvent($this->getModule(), 'after_add_category', array('item' => $category));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&report=added-category&var=' .
                    urlencode($category->getTitle()) . '&highlight=row-' . $category->getId()
                );
            }
        }
    }
}
