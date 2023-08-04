<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\DeleteMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\UpdateMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;
use Exception;

/**
 * This action is used to update one or more media items (move, ...)
 */
class MediaItemMassAction extends BackendBaseAction
{
    const MOVE = 'move';
    const DELETE = 'delete';

    private static $actions = [
        self::MOVE,
        self::DELETE,
    ];

    /** @var MediaFolder */
    protected $moveToMediaFolder;

    public function execute(): void
    {
        parent::execute();

        $this->checkToken();

        /** @var Type $selectedType */
        $selectedType = $this->getSelectedType();

        /** @var string $action */
        $action = $this->getSelectedAction();

        // Loop ids
        foreach ($this->getSelectedMediaItemIds() as $mediaItemId) {
            try {
                /** @var MediaItem $mediaItem */
                $mediaItem = $this->get(MediaItemRepository::class)->findOneById($mediaItemId);

                switch ($action) {
                    case self::MOVE:
                        $this->move($mediaItem, $selectedType);
                        break;
                    case self::DELETE:
                        $this->delete($mediaItem);
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
        $id = $this->getRequest()->request->getInt('current_folder_id');

        try {
            /** @var MediaFolder */
            return $this->get(MediaFolderRepository::class)->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            return null;
        }
    }

    private function getMediaFolder(int $mediaFolderId, Type $selectedType): MediaFolder
    {
        try {
            return $this->get(MediaFolderRepository::class)->findOneById($mediaFolderId);
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
        $id = $this->getRequest()->request->getInt('move_to_folder_id', 0);

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
        $action = $this->getRequest()->request->get('action', self::MOVE);

        if (!in_array($action, self::$actions)) {
            throw new Exception('Action not exists');
        }

        return $action;
    }

    private function getSelectedMediaItemIds(): array
    {
        $ids = $this->getRequest()->request->get('id');

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
        return Type::fromString($this->getRequest()->request->get('from', 'image'));
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

    private function delete(MediaItem $mediaItem): void
    {
        /** @var DeleteMediaItem $deleteMediaItem */
        $deleteMediaItem = new DeleteMediaItem($mediaItem);

        // Handle the MediaItem delete
        $this->get('command_bus')->handle($deleteMediaItem);
    }
}
