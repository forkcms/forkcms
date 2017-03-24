<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\DeleteMediaItem as DeleteMediaItemCommand;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;

class MediaItemDelete extends BackendBaseActionDelete
{
    public function execute()
    {
        parent::execute();

        /** @var MediaItem $mediaItem */
        $mediaItem = $this->getMediaItem();

        /** @var DeleteMediaItemCommand $deleteMediaItem */
        $deleteMediaItem = new DeleteMediaItemCommand($mediaItem);

        // Handle the MediaItem delete
        $this->get('command_bus')->handle($deleteMediaItem);
        $this->get('event_dispatcher')->dispatch(
            MediaItemDeleted::EVENT_NAME,
            new MediaItemDeleted(
                $mediaItem
            )
        );

        return $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'deleted',
                    'var' => urlencode($mediaItem->getTitle()),
                ]
            )
        );
    }

    /**
     * Get media item
     *
     * @return MediaItem
     */
    private function getMediaItem(): MediaItem
    {
        try {
            // Define MediaItem from repository
            return $this->get('media_library.repository.item')->findOneById(
                $this->getParameter('id', 'string')
            );
        } catch (\Exception $e) {
            return $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'media-item-not-existing'
                    ]
                )
            );
        }
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
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
