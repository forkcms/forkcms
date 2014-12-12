<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\ContentBlocks\Engine\Model as BackendContentBlocksModel;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Add extends BackendBaseActionAdd
{
    /**
     * The available templates
     *
     * @var	array
     */
    private $templates = array();

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->templates = BackendContentBlocksModel::getTemplates();
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
        $this->frm = new BackendForm('add');
        $this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('text');
        $this->frm->addCheckbox('visible', true);

        // if we have multiple templates, add a dropdown to select them
        if (count($this->templates) > 1) {
            $this->frm->addDropdown('template', array_combine($this->templates, $this->templates));
        }
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();
            $fields = $this->frm->getFields();

            // validate fields
            $fields['title']->isFilled(BL::err('TitleIsRequired'));

            if ($this->frm->isCorrect()) {
                // build item
                $contentBlock = new ContentBlock();
                $contentBlock
                    ->setId(BackendContentBlocksModel::getMaximumId() + 1)
                    ->setUserId(BackendAuthentication::getUser()->getUserId())
                    ->setTemplate(count($this->templates) > 1 ? $fields['template']->getValue() : $this->templates[0])
                    ->setLanguage(BL::getWorkingLanguage())
                    ->setTitle($fields['title']->getValue())
                    ->setText($fields['text']->getValue())
                    ->setIsHidden(!$fields['visible']->isChecked())
                    ->setstatus(ContentBlock::STATUS_ACTIVE)
                ;

                // insert the item
                BackendContentBlocksModel::insert($contentBlock);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $contentBlock));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=added&var=' .
                    urlencode($contentBlock->getTitle()) . '&highlight=row-' . $contentBlock->getId()
                );
            }
        }
    }
}
