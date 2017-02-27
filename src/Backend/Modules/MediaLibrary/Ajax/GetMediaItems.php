<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * This AJAX-action will get all media items in a certain folder and from a gallery.
 */
class GetMediaItems extends BackendBaseAJAXAction
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

        // Init media items
        $mediaItemsToArray = array();

        // Get media for the given folder
        if ($mediaFolder !== null) {
            /** @var MediaItem[] $mediaItems */
            $mediaItems = $this->get('media_library.repository.item')->getAllByFolder($mediaFolder);

            // Init media items array
            $mediaItemsToArray = array();
            foreach ($mediaItems as $mediaItem) {
                $mediaItemsToArray[] = $mediaItem->__toArray();
            }
        }

        /** @var int|null $mediaFolderId */
        $mediaFolderId = ($mediaFolder !== null) ? $mediaFolder->getId() : null;

        // Output success message with variables
        $this->output(
            self::OK,
            array(
                'media' => $mediaItemsToArray,
                'folder' => $mediaFolderId,
                'tab' => $this->selectedTab
            )
        );
    }

    /**
     * @return MediaGroup|null
     */
    protected function getMediaGroup()
    {
        /** @var string $id */
        $id = trim(\SpoonFilter::getPostValue('group_id', null, null, 'string'));

        // We have an id
        if ($id !== null) {
            try {
                /** @var MediaGroup */
                return $this->get('media_library.repository.group')->getOneById($id);
            } catch (\Exception $e) {
                // Throw output error
                $this->output(
                    self::BAD_REQUEST,
                    null,
                    Language::err('MediaGroupNotExists')
                );
            }
        }

        return null;
    }

    /**
     * @return MediaFolder|null
     */
    protected function getMediaFolder()
    {
        /** @var int $id */
        $id = trim(\SpoonFilter::getPostValue('folder_id', null, 0, 'int'));

        // We have an id
        if ($id !== 0) {
            try {
                /** @var MediaFolder */
                return $this->get('media_library.repository.folder')->getOneById($id);
            } catch (\Exception $e) {
                // Throw output error
                $this->output(
                    self::BAD_REQUEST,
                    null,
                    Language::err('MediaFolderNotExists')
                );
            }
        }

        return null;
    }
}
