<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\UpdateMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;
use Exception;

/**
 * This action is used to update one or more media items (move, ...)
 */
class MediaItemMassAction extends BackendBaseAction
{
    const MOVE = 'move';

    /** @var MediaFolder */
    protected $moveToMediaFolder;

    public function execute(): void
    {
        parent::execute();

        /** @var Type $selectedType */
        $selectedType = $this->getSelectedType();

        /** @var string $action */
        $action = $this->getSelectedAction();

        // Loop ids
        foreach ($this->getSelectedMediaItemIds() as $mediaItemId) {
            try {
                /** @var MediaItem $mediaItem */
                $mediaItem = $this->get('media_library.repository.item')->findOneById($mediaItemId);

                switch ($action) {
                    case self::MOVE:
                        $this->move($mediaItem, $selectedType);
                        break;
                }
            } catch (MediaItemNotFound $mediaItemNotFound) {
                // Do nothing
            }
        }

        $this->redirect(
            $this->getBackLink(
                $this->getCurrentMediaFolder(),
                [
                    'report' => 'media-' . ($action === self::MOVE ? 'moved' : 'deleted'),
                ],
                $selectedType
            )
        );
    }

    private function getBackLink(
        MediaFolder $mediaFolder = null,
        array $parameters = [],
        Type $selectedType = null
    ): string {
        if ($mediaFolder instanceof MediaFolder) {
            $parameters['folder'] = $mediaFolder->getId();
        }

        $url = Model::createUrlForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );

        if ($selectedType instanceof Type) {
            $url .= '#tab' . ucfirst((string) $selectedType);
        }

        return $url;
    }

    private function getCurrentMediaFolder(): ?MediaFolder
    {
        // Define current folder
        $id = $this->getRequest()->query->getInt('current_folder_id');

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            return null;
        }
    }

    private function getMediaFolder(int $mediaFolderId, Type $selectedType): MediaFolder
    {
        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($mediaFolderId);
        } catch (MediaItemNotFound $mediaItemNotFound) {
            $this->redirect(
                $this->getBackLink(
                    null,
                    [
                        'error' => 'folder-does-not-exists',
                    ],
                    $selectedType
                )
            );
        }
    }

    private function getMediaFolderToMoveTo(Type $selectedType): MediaFolder
    {
        // Define folder id
        $id = $this->getRequest()->query->getInt('move_to_folder_id', 0);

        if ($id === 0) {
            $this->redirect(
                $this->getBackLink(
                    null,
                    [
                        'error' => 'please-select-a-folder',
                    ],
                    $selectedType
                )
            );
        }

        return $this->getMediaFolder($id, $selectedType);
    }

    private function getSelectedAction(): string
    {
        $action = $this->getRequest()->query->get('action', self::MOVE);

        if (self::MOVE !== $action) {
            throw new Exception('Action not exists');
        }

        return $action;
    }

    private function getSelectedMediaItemIds(): array
    {
        $ids = $this->getRequest()->query->get('id');

        if (empty($ids)) {
            $this->redirect(
                $this->getBackLink(
                    null,
                    [
                        'error' => 'no-files-selected',
                    ]
                )
            );
        }

        return $ids;
    }

    private function getSelectedType(): Type
    {
        return Type::fromString($this->getRequest()->query->get('from', 'image'));
    }

    private function move(MediaItem $mediaItem, Type $selectedType): void
    {
        if ($this->moveToMediaFolder === null) {
            /** @var MediaFolder $moveToMediaFolder */
            $this->moveToMediaFolder = $this->getMediaFolderToMoveTo($selectedType);
        }

        /** @var UpdateMediaItem $updateMediaItem */
        $updateMediaItem = new UpdateMediaItem($mediaItem);
        $updateMediaItem->folder = $this->moveToMediaFolder;

        // Handle the MediaItem update
        $this->get('command_bus')->handle($updateMediaItem);
    }
}
