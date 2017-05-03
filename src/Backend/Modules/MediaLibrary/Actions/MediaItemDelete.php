<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class MediaItemDelete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        parent::execute();

        /** @var MediaItem $mediaItem */
        $mediaItem = $this->getMediaItem();

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

    private function getMediaItem(): MediaItem
    {
        try {
            // Define MediaItem from repository
            return $this->get('media_library.repository.item')->findOneById(
                $this->getParameter('id', 'string')
            );
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
        return Model::createURLForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );
    }
}
