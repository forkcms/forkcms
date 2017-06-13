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
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Command\UpdateContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Event\ContentBlockUpdated;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRevisionDataGrid;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Exception\ContentBlockNotFound;
use Symfony\Component\Form\Form;

/**
 * This is the edit-action, it will display a form to edit an existing item
 */
class ContentBlockEdit extends BackendBaseActionEdit
{
    public function execute(): void
    {
        parent::execute();

        /** @var ContentBlock $contentBlock */
        $contentBlock = $this->getContentBlock();

        /** @var Form $form */
        $form = $this->getForm($contentBlock);

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());
            $this->tpl->assign('contentBlock', $contentBlock);
            $this->tpl->assign('revisions', ContentBlockRevisionDataGrid::getHtml($contentBlock, Locale::workingLocale()));

            $this->parse();
            $this->display();

            return;
        }

        /** @var UpdateContentBlock $updateContentBlock */
        $updateContentBlock = $this->updateContentBlock($form);

        $this->get('event_dispatcher')->dispatch(
            ContentBlockUpdated::EVENT_NAME,
            new ContentBlockUpdated($updateContentBlock->getContentBlockEntity())
        );

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'content-block-edited',
                    'var' => $updateContentBlock->title,
                    'highlight' => 'row-' . $contentBlock->getId(),
                ]
            )
        );
    }

    private function getBackLink(array $parameters = []): string
    {
        return BackendModel::createURLForAction(
            'ContentBlockIndex',
            null,
            null,
            $parameters
        );
    }

    private function getContentBlock(): ContentBlock
    {
        /** @var ContentBlockRepository $contentBlockRepository */
        $contentBlockRepository = $this->get('content_blocks.repository.content_block');

        // specific revision?
        $revisionId = $this->getParameter('revision', 'int');

        if ($revisionId !== null) {
            $this->tpl->assign('usingRevision', true);

            try {
                return $contentBlockRepository->findOneByRevisionIdAndLocale($revisionId, Locale::workingLocale());
            } catch (ContentBlockNotFound $e) {
                $this->redirect($this->getBackLink(['error' => 'non-existing']));
            }
        }

        try {
            return $contentBlockRepository->findOneByIdAndLocale($this->getParameter('id', 'int'), Locale::workingLocale());
        } catch (ContentBlockNotFound $e) {
            $this->redirect($this->getBackLink(['error' => 'non-existing']));
        }
    }

    private function getForm(ContentBlock $contentBlock): Form
    {
        $form = $this->createForm(
            ContentBlockType::class,
            new UpdateContentBlock($contentBlock),
            [
                'theme' => $this->get('fork.settings')->get('Core', 'theme', 'Core'),
            ]
        );

        $form->handleRequest($this->get('request'));

        return $form;
    }

    private function updateContentBlock(Form $form): UpdateContentBlock
    {
        /** @var UpdateContentBlock $updateContentBlock */
        $updateContentBlock = $form->getData();
        $updateContentBlock->userId = Authentication::getUser()->getUserId();

        // The command bus will handle the saving of the content block in the database.
        $this->get('command_bus')->handle($updateContentBlock);

        return $updateContentBlock;
    }
}
