<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\UpdateMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\DeleteMediaItem as DeleteMediaItemCommand;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemUpdated;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;

/**
 * This action is used to update one or more media items (status, delete, ...)
 */
class MediaItemMassAction extends BackendBaseAction
{
    const MOVE = 'move';
    const DELETE = 'delete';

    /** @var MediaFolder */
    protected $moveToMediaFolder;

    public function execute()
    {
        parent::execute();

        /** @var array $mediaItemIds */
        $ids = $this->getSelectedMediaItemIds();

        /** @var Type $selectedType */
        $selectedType = $this->getSelectedType();

        /** @var MediaFolder|null $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        /** @var string $action */
        $action = $this->getSelectedAction();

        // Init parameters
        $parameters = [];

        // We have a current MediaFolder
        if ($mediaFolder !== null) {
            $parameters['folder'] = $mediaFolder->getId();
        }

        // Loop ids
        foreach ($ids as $mediaItemId) {
            try {
                /** @var MediaItem $mediaItem */
                $mediaItem = $this->get('media_library.repository.item')->findOneById($mediaItemId);

                switch ($action) {
                    case self::MOVE:
                        // If not yet set
                        if ($this->moveToMediaFolder === null) {
                            /** @var MediaFolder $moveToMediaFolder */
                            $this->moveToMediaFolder = $this->getMediaFolderToMoveTo($selectedType);
                        }

                        $mediaItem->setFolder($this->moveToMediaFolder);

                        /** @var UpdateMediaItem $updateMediaItem */
                        $updateMediaItem = new UpdateMediaItem($mediaItem);

                        // Handle the MediaItem update
                        $this->get('command_bus')->handle($updateMediaItem);
                        $this->get('event_dispatcher')->dispatch(
                            MediaItemUpdated::EVENT_NAME,
                            new MediaItemUpdated($updateMediaItem->getMediaItemEntity())
                        );

                        break;
                    case self::DELETE:
                        /** @var DeleteMediaItemCommand $deleteMediaItem */
                        $deleteMediaItem = new DeleteMediaItemCommand($mediaItem);

                        // Handle the MediaItem delete
                        $this->get('command_bus')->handle($deleteMediaItem);
                        $this->get('event_dispatcher')->dispatch(
                            MediaItemDeleted::EVENT_NAME,
                            new MediaItemDeleted($deleteMediaItem->mediaItem)
                        );

                        break;
                }
            } catch (\Exception $e) {
                // Do nothing
            }
        }

        $parameters['report'] = 'media-' . ($action === self::MOVE ? 'moved' : 'deleted');

        $this->redirect($this->getBackLink($parameters) . '#tab' . ucfirst($selectedType));
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

    /**
     * @return MediaFolder|null
     */
    private function getMediaFolder()
    {
        // Define current folder
        $id = $this->get('request')->query->get('current_folder_id');

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById((int) $id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param Type $selectedType
     * @return MediaFolder
     */
    private function getMediaFolderToMoveTo(Type $selectedType): MediaFolder
    {
        // Define folder id
        $id = (int) $this->get('request')->query->get('move_to_folder_id', 0);

        if ($id === 0) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'please-select-a-folder',
                    ]
                )
                . '#tab' . ucfirst((string) $selectedType)
            );
        }

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (\Exception $e) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'folder-does-not-exists',
                    ]
                )
                . '#tab' . ucfirst((string) $selectedType)
            );
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getSelectedAction(): string
    {
        $action = $this->get('request')->query->get('action', self::MOVE);

        if (!in_array($action, [self::MOVE, self::DELETE])) {
            throw new \Exception('Action not exists');
        }

        return $action;
    }

    /**
     * @return array
     */
    private function getSelectedMediaItemIds(): array
    {
        $ids = $this->get('request')->query->get('id');

        if (empty($ids)) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'no-files-selected'
                    ]
                )
            );
        }

        return $ids;
    }

    /**
     * @return Type
     */
    private function getSelectedType(): Type
    {
        return Type::fromString($this->get('request')->query->get('from', 'image'));
    }
}
