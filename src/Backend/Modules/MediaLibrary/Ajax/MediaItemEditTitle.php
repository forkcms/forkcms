<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\UpdateMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Common\Exception\AjaxExitException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This AJAX-action is being used to edit the title for an existing MediaItem item and save them into to the database.
 */
class MediaItemEditTitle extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        /** @var UpdateMediaItem $updateMediaItem */
        $updateMediaItem = $this->updateMediaItem();

        // Output
        $this->output(
            Response::HTTP_OK,
            $updateMediaItem->getMediaItemEntity(),
            sprintf(
                Language::msg('MediaItemEdited'),
                $updateMediaItem->getMediaItemEntity()->getTitle()
            )
        );
    }

    protected function getMediaItem(): MediaItem
    {
        $id = $this->getRequest()->request->get('media_id');

        // validate values
        if ($id === null) {
            throw new AjaxExitException(Language::err('MediaItemIdIsRequired'));
        }

        try {
            /** @var MediaItem $mediaItem */
            return $this->get('media_library.repository.item')->findOneById($id);
        } catch (MediaItemNotFound $mediaItemNotFound) {
            throw new AjaxExitException(Language::err('MediaItemDoesNotExists'));
        }
    }

    protected function getItemTitle(): ?string
    {
        return $this->getRequest()->request->get('title');
    }

    private function updateMediaItem(): UpdateMediaItem
    {
        /** @var MediaItem $mediaItem */
        $mediaItem = $this->getMediaItem();

        /** @var string $title */
        $title = $this->getItemTitle();

        /** @var UpdateMediaItem $updateMediaItem */
        $updateMediaItem = new UpdateMediaItem($mediaItem);
        $updateMediaItem->title = $title;

        // Handle the MediaItem update
        $this->get('command_bus')->handle($updateMediaItem);

        return $updateMediaItem;
    }
}
