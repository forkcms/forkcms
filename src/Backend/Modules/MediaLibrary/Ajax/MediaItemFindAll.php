<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Common\Exception\AjaxExitException;

/**
 * This AJAX-action will get all media items in a certain folder and from a gallery.
 */
class MediaItemFindAll extends BackendBaseAJAXAction
{
    /** @var string */
    protected $selectedTab = 'image';

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaFolder|null $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        /** @var MediaGroup|null $mediaGroup */
        $mediaGroup = $this->getMediaGroup();

        // We didn't get a folder, so we must get the folder from the first connected MediaGroupMediaItem entity
        if ($mediaFolder === null && $mediaGroup !== null && $mediaGroup->getConnectedItems()->count() > 0) {
            /** @var MediaItem $mediaItem The first item of the gallery MediaGroup */
            $mediaItem = $mediaGroup->getConnectedItems()->first()->getItem();

            // Redefine folder
            $mediaFolder = $mediaItem->getFolder();

            // Redefine tab
            $this->selectedTab = (string) $mediaItem->getType();
        }

        /** @var int|null $mediaFolderId */
        $mediaFolderId = $mediaFolder !== null ? $mediaFolder->getId() : null;

        // Init media items
        $mediaItemsToArray = [];

        // Get media for the given folder
        if ($mediaFolder !== null) {
            /** @var MediaItem[] $mediaItems */
            $mediaItems = $this->get('media_library.repository.item')->findByFolder($mediaFolder);

            // Init media items array
            $mediaItemsToArray = [];
            foreach ($mediaItems as $mediaItem) {
                $mediaItemsToArray[] = $mediaItem->__toArray();
            }
        }

        // Output success message with variables
        $this->output(
            self::OK,
            [
                'media' => $mediaItemsToArray,
                'folder' => $mediaFolderId,
                'tab' => $this->selectedTab
            ]
        );
    }

    /**
     * @return MediaGroup|null
     * @throws AjaxExitException
     */
    protected function getMediaGroup()
    {
        /** @var string $id */
        $id = $this->get('request')->request->get('group_id', '');

        if ($id === '') {
            return null;
        }

        try {
            /** @var MediaGroup */
            return $this->get('media_library.repository.group')->findOneById($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return MediaFolder|null
     * @throws AjaxExitException
     */
    protected function getMediaFolder()
    {
        /** @var int $id */
        $id = $this->get('request')->request->getInt('folder_id', 0);

        if ($id === 0) {
            return null;
        }

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (\Exception $e) {
            throw new AjaxExitException(Language::err('MediaFolderNotExists'));
        }
    }
}
