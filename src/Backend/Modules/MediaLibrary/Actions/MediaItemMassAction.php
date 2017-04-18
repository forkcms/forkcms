<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\UpdateMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;

/**
 * This action is used to update one or more media items (move, ...)
 */
class MediaItemMassAction extends BackendBaseAction
{
    const MOVE = 'move';

    /** @var MediaFolder */
    protected $moveToMediaFolder;

    public function execute()
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
            } catch (\Exception $e) {
                // Do nothing
            }
        }

        $this->redirect(
            $this->getBackLink(
                $this->getMediaFolder(),
                [
                    'report' => 'media-' . ($action === self::MOVE ? 'moved' : 'deleted')
                ],
                $selectedType
            )
        );
    }

    /**
     * @param MediaFolder $mediaFolder
     * @param array $parameters
     * @param Type|null $selectedType
     * @return string
     */
    private function getBackLink(MediaFolder $mediaFolder = null, array $parameters = [], Type $selectedType = null): string
    {
        if ($mediaFolder instanceof MediaFolder) {
            $parameters['folder'] = $mediaFolder->getId();
        }

        $URL = Model::createURLForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );

        if ($selectedType instanceof Type) {
            $URL .= '#tab' . ucfirst((string) $selectedType);
        }

        return $URL;
    }

    /**
     * @return MediaFolder|null
     */
    private function getMediaFolder()
    {
        // Define current folder
        $id = $this->get('request')->query->getInt('current_folder_id');

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
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
        $id = $this->get('request')->query->getInt('move_to_folder_id', 0);

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

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (\Exception $e) {
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

    /**
     * @return string
     * @throws \Exception
     */
    private function getSelectedAction(): string
    {
        $action = $this->get('request')->query->get('action', self::MOVE);

        if (!in_array($action, [self::MOVE])) {
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
                    null,
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

    /**
     * @param MediaItem $mediaItem
     * @param Type $selectedType
     */
    private function move(MediaItem $mediaItem, Type $selectedType)
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
