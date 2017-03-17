<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\DataGridFunctions;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language;
use Backend\Modules\ContentBlocks\Command\UpdateContentBlock;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Event\ContentBlockUpdated;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;
use Backend\Modules\ContentBlocks\Form\ContentBlockType;
use Backend\Modules\ContentBlocks\DataGrid\ContentBlockRevisionDataGrid;
use Backend\Core\Language\Locale;
use SpoonFilter;

/**
 * This is the edit-action, it will display a form to edit an existing item
 */
class Edit extends BackendBaseActionEdit
{
    /** @var ContentBlock */
    private $contentBlock;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->contentBlock = $this->getContentBlock();

        if ($this->contentBlock === null) {
            return $this->redirect(BackendModel::createURLForAction('Index', null, null, ['error' => 'non-existing']));
        }

        $form = $this->createForm(
            new ContentBlockType($this->get('fork.settings')->get('Core', 'theme', 'core'), UpdateContentBlock::class),
            new UpdateContentBlock($this->contentBlock)
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

            $this->parse();
            $this->display();

            return;
        }

        /** @var UpdateContentBlock $updateContentBlock */
        $updateContentBlock = $form->getData();
        $updateContentBlock->userId = Authentication::getUser()->getUserId();

        // The command bus will handle the saving of the content block in the database.
        $this->get('command_bus')->handle($updateContentBlock);

        $this->get('event_dispatcher')->dispatch(
            ContentBlockUpdated::EVENT_NAME,
            new ContentBlockUpdated($updateContentBlock->contentBlock)
        );

        return $this->redirect(
            BackendModel::createURLForAction(
                'Index',
                null,
                null,
                [
                    'report' => 'edited',
                    'var' => $updateContentBlock->title,
                    'highlight' => 'row-' . $this->contentBlock->getId(),
                ]
            )
        );
    }

    /**
     * @return ContentBlock|null
     */
    private function getContentBlock()
    {
        /** @var ContentBlockRepository $contentBlockRepository */
        $contentBlockRepository = $this->get('content_blocks.repository.content_block');

        // specific revision?
        $revisionId = $this->getParameter('revision', 'int');

        if ($revisionId !== null) {
            $this->tpl->assign('usingRevision', true);

            return $contentBlockRepository->findOneByRevisionIdAndLocale($revisionId, Locale::workingLocale());
        }

        return $contentBlockRepository->findOneByIdAndLocale($this->getParameter('id', 'int'), Locale::workingLocale());
    }

    /**
     * Parses a data grid with the revisions in the template
     */
    private function parseRevisionsDataGrid()
    {
        // create datagrid
        $revisions = new ContentBlockRevisionDataGrid($this->contentBlock, Locale::workingLocale());

        // hide columns
        $revisions->setColumnsHidden(['id', 'revision_id']);

        // disable paging
        $revisions->setPaging(false);

        // set headers
        $revisions->setHeaderLabels(
            [
                'user_id' => SpoonFilter::ucfirst(Language::lbl('By')),
                'edited_on' => SpoonFilter::ucfirst(Language::lbl('LastEditedOn')),
            ]
        );

        // set column-functions
        $revisions->setColumnFunction([DataGridFunctions::class, 'getUser'], ['[user_id]'], 'user_id');
        $revisions->setColumnFunction([DataGridFunctions::class, 'getTimeAgo'], ['[edited_on]'], 'edited_on');

        // check if this action is allowed
        if (Authentication::isAllowedAction('Edit')) {
            $editRevisionUrl = BackendModel::createURLForAction(
                'Edit',
                null,
                null,
                ['id' => '[id]', 'revision' => '[revision_id]'],
                false
            );
            // set column URLs
            $revisions->setColumnURL('title', $editRevisionUrl);

            // add use column
            $revisions->addColumn(
                'use_revision',
                null,
                Language::lbl('UseThisVersion'),
                $editRevisionUrl,
                Language::lbl('UseThisVersion')
            );
        }

        $this->tpl->assign('revisions', (string) $revisions->getContent());
    }

    /**
     * Parse the content block and the revisions
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('id', $this->contentBlock->getId());
        $this->tpl->assign('title', $this->contentBlock->getTitle());
        $this->tpl->assign('revision_id', $this->contentBlock->getRevisionId());

        $this->parseRevisionsDataGrid();
    }
}
