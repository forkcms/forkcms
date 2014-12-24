<?php

namespace Backend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class EditCategory extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // does the item exist?
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
        $this->record = BackendFaqModel::getCategory($this->id);

        if ($this->id == null || empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=non-existing');
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('editCategory');
        $this->frm->addText('title', $this->record->getTitle());

        $this->meta = new BackendMeta($this->frm, $this->record->getMeta(), 'title', true);
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // assign the data
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign(
            'showFaqDeleteCategory',
            (
                BackendFaqModel::deleteCategoryAllowed($this->record) &&
                BackendAuthentication::isAllowedAction('DeleteCategory')
            )
        );
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->meta->setUrlCallback(
                'Backend\Modules\Faq\Engine\Model',
                'getURLForCategory',
                array($this->id)
            );

            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                $this->record
                    ->setTitle($this->frm->getField('title')->getValue())
                    ->setMeta($this->meta->save(true))
                ;

                // update the item
                BackendFaqModel::updateCategory($this->record);
                BackendModel::triggerEvent($this->getModule(), 'after_edit_category', array('item' => $this->record));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&report=edited-category&var=' .
                    urlencode($this->record->getTitle()) . '&highlight=row-' . $this->record->getId()
                );
            }
        }
    }
}
