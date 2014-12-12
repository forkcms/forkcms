<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\DataGridDoctrine;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\ContentBlocks\Engine\Model as BackendContentBlocksModel;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class Edit extends BackendBaseActionEdit
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
        $this->getData();
        $this->loadRevisions();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Get the data
     * If a revision-id was specified in the URL we load the revision and not the most recent data.
     */
    private function getData()
    {
        $this->id = $this->getParameter('id', 'int');
        $this->record = BackendContentBlocksModel::get($this->id);

        // specific revision?
        $revisionToLoad = $this->getParameter('revision', 'int');

        // if this is a valid revision
        if ($revisionToLoad !== null) {
            // overwrite the current record
            $this->record = BackendContentBlocksModel::getRevision($this->id, $revisionToLoad);

            // show warning
            $this->tpl->assign('usingRevision', true);
        }

        if ($this->id == null || empty($this->record)) {
            // no item found, throw an exceptions, because somebody is fucking with our url
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        // get the templates
        // @todo why is $this->templates loaded twice?
        $this->templates = BackendContentBlocksModel::getTemplates();

        // check if selected template is still available
        if ($this->record->getTemplate() && !in_array($this->record->getTemplate(), $this->templates)) {
            $this->record->setTemplate('');
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('edit');
        $this->frm->addText('title', $this->record->getTitle(), null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('text', $this->record->getText());
        $this->frm->addCheckbox('visible', !$this->record->getIsHidden());

        // if we have multiple templates, add a dropdown to select them
        if (count($this->templates) > 1) {
            $this->frm->addDropdown('template', array_combine($this->templates, $this->templates), $this->record->getTemplate());
        }
    }

    /**
     * Load the datagrid with revisions
     */
    private function loadRevisions()
    {
        $this->dgRevisions = new DataGridDoctrine(
            BackendContentBlocksModel::ENTITY_CLASS,
            array(
                'status'   => ContentBlock::STATUS_ARCHIVED,
                'id'       => $this->record->getId(),
                'language' => BL::getWorkingLanguage(),
            ),
            array(
                'id',
                'revisionId' => 'revision_id',
                'title',
                'editedOn'   => 'edited_on',
                'userId'     => 'user_id',
            )
        );

        // hide columns
        $this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));

        // disable paging
        $this->dgRevisions->setPaging(false);

        // set headers
        $this->dgRevisions->setHeaderLabels(array(
            'user_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
            'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEditedOn'))
        ));

        // set column-functions
        $this->dgRevisions->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getUser'),
            array('[user_id]'),
            'user_id'
        );
        $this->dgRevisions->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            array('[edited_on]'),
            'edited_on'
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgRevisions->setColumnURL(
                'title',
                BackendModel::createURLForAction('Edit') .
                '&amp;id=[id]&amp;revision=[revision_id]'
            );

            // add use column
            $this->dgRevisions->addColumn(
                'use_revision',
                null,
                BL::lbl('UseThisVersion'),
                BackendModel::createURLForAction('Edit') .
                '&amp;id=[id]&amp;revision=[revision_id]',
                BL::lbl('UseThisVersion')
            );
        }
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('item', $this->record);

        // assign revisions-datagrid
        $this->tpl->assign('revisions', (string) $this->dgRevisions->getContent());
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
                $contentBlock = new ContentBlock();
                $contentBlock
                    ->setId($this->id)
                    ->setExtraId($this->record->getExtraId())
                    ->setUserId(BackendAuthentication::getUser()->getUserId())
                    ->setTemplate(count($this->templates) > 1 ? $fields['template']->getValue() : $this->templates[0])
                    ->setLanguage(BL::getWorkingLanguage())
                    ->setTitle($fields['title']->getValue())
                    ->setText($fields['text']->getValue())
                    ->setIsHidden(!$fields['visible']->isChecked())
                    ->setstatus(ContentBlock::STATUS_ACTIVE)
                ;

                // insert the item
                BackendContentBlocksModel::update($contentBlock);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $contentBlock));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=edited&var=' .
                    urlencode($contentBlock->getTitle()) . '&highlight=row-' . $contentBlock->getId()
                );
            }
        }
    }
}
