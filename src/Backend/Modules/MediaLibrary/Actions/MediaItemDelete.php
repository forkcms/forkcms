<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use ForkCMS\Backend\Core\Engine\Model;
use ForkCMS\Backend\Form\Type\DeleteType;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class MediaItemDelete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        parent::execute();

        $deleteForm = $this->createForm(
            DeleteType::class,
            null,
            ['module' => $this->getModule(), 'action' => 'MediaItemDelete']
        );
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(Model::createUrlForAction('MediaItemIndex') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        /** @var MediaItem $mediaItem */
        $mediaItem = $this->getMediaItem($deleteFormData['id']);

        // Handle the MediaItem delete
        $this->get('media_library.manager.item')->delete($mediaItem);

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'media-item-deleted',
                    'var' => urlencode($mediaItem->getTitle()),
                ]
            )
        );
    }

    private function getMediaItem(string $id): MediaItem
    {
        try {
            // Define MediaItem from repository
            return $this->get('media_library.repository.item')->findOneById($id);
        } catch (MediaItemNotFound $mediaItemNotFound) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'media-item-not-existing',
                    ]
                )
            );
        }
    }

    private function getBackLink(array $parameters = []): string
    {
        return Model::createUrlForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );
    }
}
