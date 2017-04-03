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
        $mediaFolder = $this->getMediaFolderBasedOnMediaGroup();

        // Output success message with variables
        $this->output(
            self::OK,
            [
                'media' => $this->loadMediaItems($mediaFolder),
                'folder' => $mediaFolder !== null ? $mediaFolder->getId() : null,
                'tab' => $this->selectedTab
            ]
        );
    }

    /**
     * @return MediaFolder|null
     * @throws AjaxExitException
     */
    private function getMediaFolder()
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

    /**
     * @return MediaFolder|null
     */
    private function getMediaFolderBasedOnMediaGroup()
    {
        /** @var MediaGroup|null $mediaGroup */
        $mediaGroup = $this->getMediaGroup();

        /** @var MediaFolder|null $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        if ($mediaFolder !== null || $mediaGroup === null || $mediaGroup->getConnectedItems()->count() == 0) {
            return $mediaFolder;
        }

        /** @var MediaItem $mediaItem The first item of the gallery MediaGroup */
        $mediaItem = $mediaGroup->getConnectedItems()->first()->getItem();

        // Redefine tab
        $this->selectedTab = (string) $mediaItem->getType();

        // Redefine folder
        return $mediaItem->getFolder();
    }

    /**
     * @return MediaGroup|null
     * @throws AjaxExitException
     */
    private function getMediaGroup()
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
     * @param MediaFolder|null $mediaFolder
     * @return array
     */
    private function loadMediaItems(MediaFolder $mediaFolder = null): array
    {
        if ($mediaFolder === null) {
            return [];
        }

        return array_map(
            function (MediaItem $mediaItem) {
                return $mediaItem->__toArray();
            },
            $this->get('media_library.repository.item')->findByFolder($mediaFolder)
        );
    }
}
