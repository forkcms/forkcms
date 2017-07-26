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
use Backend\Core\Language\Locale;
use Backend\Form\Type\DeleteType;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Command\UpdateContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRevisionDataGrid;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Event\ContentBlockUpdated;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Exception\ContentBlockNotFound;
use Symfony\Component\Form\Form;

/**
 * This is the edit-action, it will display a form to edit an existing item
 */
class Edit extends BackendBaseActionEdit
{
    public function execute(): void
    {
        parent::execute();

        $contentBlock = $this->getContentBlock();

        $form = $this->getForm($contentBlock);

        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $contentBlock->getId()],
            ['module' => $this->getModule()]
        );
        $this->template->assign('deleteForm', $deleteForm->createView());

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->template->assign('form', $form->createView());
            $this->template->assign('contentBlock', $contentBlock);
            $this->template->assign('revisions', ContentBlockRevisionDataGrid::getHtml($contentBlock, Locale::workingLocale()));

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
                    'report' => 'edited',
                    'var' => $updateContentBlock->title,
                    'highlight' => 'row-' . $contentBlock->getId(),
                ]
            )
        );
    }

    private function getBackLink(array $parameters = []): string
    {
        return BackendModel::createUrlForAction(
            'Index',
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
        $revisionId = $this->getRequest()->query->getInt('revision');

        if ($revisionId !== 0) {
            $this->template->assign('usingRevision', true);

            try {
                return $contentBlockRepository->findOneByRevisionIdAndLocale($revisionId, Locale::workingLocale());
            } catch (ContentBlockNotFound $e) {
                $this->redirect($this->getBackLink(['error' => 'non-existing']));
            }
        }

        try {
            return $contentBlockRepository->findOneByIdAndLocale(
                $this->getRequest()->query->getInt('id'),
                Locale::workingLocale()
            );
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
                'theme' => $this->get('fork.settings')->get('Core', 'theme', 'Fork'),
            ]
        );

        $form->handleRequest($this->getRequest());

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
