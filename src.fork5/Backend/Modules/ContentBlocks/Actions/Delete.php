<?php

namespace Backend\Modules\ContentBlocks\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Locale;
use Backend\Form\Type\DeleteType;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Command\DeleteContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Event\ContentBlockDeleted;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Exception\ContentBlockNotFound;

/**
 * This is the delete-action, it will delete an item.
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(DeleteType::class, null, ['module' => $this->getModule()]);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }
        $deleteFormData = $deleteForm->getData();

        $contentBlock = $this->getContentBlock((int) $deleteFormData['id']);

        // The command bus will handle the saving of the content block in the database.
        $this->get('command_bus')->handle(new DeleteContentBlock($contentBlock));

        $this->get('event_dispatcher')->dispatch(
            ContentBlockDeleted::EVENT_NAME,
            new ContentBlockDeleted($contentBlock)
        );

        $this->redirect($this->getBackLink(['report' => 'deleted', 'var' => $contentBlock->getTitle()]));
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

    private function getContentBlock(int $id): ContentBlock
    {
        try {
            return $this->get(ContentBlockRepository::class)->findOneByIdAndLocale(
                $id,
                Locale::workingLocale()
            );
        } catch (ContentBlockNotFound $e) {
            $this->redirect($this->getBackLink(['error' => 'non-existing']));
        }
    }
}
